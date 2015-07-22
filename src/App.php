<?php

namespace schellie\teletekstbot;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use marijnvdwerf\teletekst\ImageFormatter;
use schellie\teletekstbot\commands\AboutCommand;
use schellie\teletekstbot\commands\DirectNavigationCommand;
use schellie\teletekstbot\commands\NavigationalCommand;
use schellie\teletekstbot\commands\WelcomeCommand;
use schellie\teletekstbot\telegram\PhotoMessage;
use schellie\teletekstbot\telegram\TextMessage;

class App
{
    private $telegramClient;
    private $teletekstClient;
    private $imageFormatter;

    public function __construct()
    {
        $this->telegramClient = new Client([
            'base_uri' => 'https://api.telegram.org/bot' . getenv('TELEGRAM_TOKEN') . '/',
        ]);
        $this->teletekstClient = new TeletekstClient();
        $this->imageFormatter = new ImageFormatter();
        $this->commandParser = new CommandParser(getenv('TELEGRAM_BOTNAME'));
    }

    private function getReply(\StdClass $update)
    {
        // Dit is de tekst van het binnenkomende bericht
        $text = $update->message->text;

        $command = $this->commandParser->parseCommand($text);

        if ($command === null) {
            return new TextMessage('Sorry, dit commando ken ik niet.');
        }

        if ($command instanceof WelcomeCommand) {
            return new TextMessage([
                'TELETEKST IN JE TELEGRAM',
                '',
                'Alle inhoud (C) NOS / overige publieke omroepen.',
                'Deze chatbot is niet verbonden aan de NOS.',
                '',
                '/<nummer> - Teletekstpagina laden',
                '/vorige - Vorige pagina',
                '/subvorige - Vorige subpagina',
                '/subvolgende - Volgende subpagina',
                '/volgende - Volgende pagina',
                '/herladen - Pagina herladen',
                '/help - Deze tekst',
                '/about - Over deze bot',
                '',
                '/101 - Nieuwsoverzicht',
                '/401 - Opmerkelijk',
                '/601 - Sport',
                '/801 - Voetbal'
            ]);
        }

        if ($command instanceof AboutCommand) {
            return new TextMessage([
                'Deze bot haalt op verzoek Teletekst-pagina\'s van de servers van de NOS.',
                '',
                'Gemaakt door @Schellevis (op Telegram/Twitter), met hulp van Jeroen Wollaars en Koen Rouwhorst. Met dank aan de NOS, die Teletekst als kant-en klare json aanbiedt.',
                '',
                'Tip: commando\'s werken ook zonder slash (/)! Behalve in groepen.',
            ]);
        }

        if ($command instanceof NavigationalCommand) {


            if (file_exists(__DIR__ . '/../storage/status/' . $update->message->chat->id . '.json')) {
                $status = file_get_contents(__DIR__ . '/../storage/status/' . $update->message->chat->id . '.json');
                $status = NavigationStatus::fromJSON(json_decode($status));
            } else {
                $status = null;
            }

            if (!$command instanceof DirectNavigationCommand && $status === null) {
                return new TextMessage('Je moet eerst een pagina opvragen voor je verder kunt navigeren');
            }


            $newPageID = $command->getTargetPageID($status);
            if ($newPageID === null) {
                return new TextMessage('Die Teletekst-pagina heb ik helaas niet kunnen vinden.');
            }

            $page = $this->teletekstClient->getPage($newPageID);
            if ($page === null) {
                return new TextMessage('Die Teletekst-pagina heb ik helaas niet kunnen vinden.');
            }

            $status = NavigationStatus::fromPage($newPageID, $page);
            file_put_contents(__DIR__ . '/../storage/status/' . $update->message->chat->id . '.json', json_encode($status));

            $relativeNavigation = [];
            if (!empty($status->prevPage) || !empty($status->nextPage)) {
                if (!empty($status->prevPage)) {
                    $relativeNavigation[1] = '<<';
                } else {
                    $relativeNavigation[1] = ' ';
                }

                if (!empty($status->nextPage)) {
                    $relativeNavigation[4] = '>>';
                } else {
                    $relativeNavigation[4] = ' ';
                }
            }
            if (!empty($status->prevSubPage) || !empty($status->nextSubPage)) {
                if (!empty($status->prevSubPage)) {
                    $relativeNavigation[2] = '<';
                } else {
                    $relativeNavigation[2] = ' ';
                }

                if (!empty($status->nextSubPage)) {
                    $relativeNavigation[3] = '>';
                } else {
                    $relativeNavigation[3] = ' ';
                }
            }

            // get rid of the numeric indexes
            ksort($relativeNavigation);
            $relativeNavigation = array_values($relativeNavigation);

            $gd = $this->imageFormatter->formatPage($page->content);

            $width = 512;
            $height = 512;
            $gif = imagecreatetruecolor($width, $height);
            imagecopyresampled($gif, $gd, 0, 0, 0, 0, $width, $height, 328, 400);
            $imagePath = __DIR__ . '/../storage/tmp/' . $newPageID . '.gif';
            imagegif($gif, $imagePath);

            return (new PhotoMessage($imagePath))->withReplyMarkup([$relativeNavigation]);
        }

        return new TextMessage(get_class($command));
    }

    private function formatUserName(\StdClass $user)
    {
        if (isset($user->first_name) || isset($user->last_name)) {
            $name = [];

            if (isset($user->first_name) && !empty($user->first_name)) {
                $name[] = trim($user->first_name);
            }

            if (isset($user->last_name) && !empty($user->last_name)) {
                $name[] = trim($user->last_name);
            }

            if (isset($user->username) && !empty($user->username)) {
                $name[] = '(' . trim($user->username) . ')';
            }

            array_filter($name);

            return implode(' ', $name);
        }

        if (isset($user->username)) {
            return $user->username;
        }

        return '#' . $user->id;
    }

    private function logUpdate(\StdClass $update)
    {
        $name = $this->formatUserName($update->message->from);
        if (isset($update->message->group_chat_created) && $update->message->group_chat_created === true) {
            echo sprintf("\033[0;32m%s heeft de groep ‘%s’ aangemaakt.\033[0m" . PHP_EOL, $name, $update->message->chat->title);
            return;
        }

        if (isset($update->message->new_chat_title)) {
            echo sprintf("\033[0;32m%s heeft de groepsnaam gewijzigd naar ‘%s’.\033[0m" . PHP_EOL, $name, $update->message->chat->title);
            return;
        }

        if (isset($update->message->chat->title)) {
            $label = $name . ' in ‘' . $update->message->chat->title . '’';
        } else {
            $label = $name;
        }

        echo "\033[0;34m" . $label . ':' . "\033[0m" . ' ' . $update->message->text . PHP_EOL;
    }

    public function processUpdate(\StdClass $update)
    {
        $this->logUpdate($update);

        if (!isset($update->message->text)) {
            // No text, so no need to act.
            return;
        }

        $reply = $this->getReply($update);
        if (is_null($reply)) {
            // Nothing to send
            return;
        }

        $reply->setReplyToMessageID($update->message->message_id);

        try {
            $this->telegramClient->send($reply->getRequest($update->message->chat->id)); // open het URL en doe verder niets
        } catch (ClientException $e) {
            dump($e);
            dump((string)$e->getResponse()->getBody());
        } catch (ServerException $e) {
            dump($e->getRequest());
            dump((string)$e->getResponse()->getBody());
        }
    }
}
