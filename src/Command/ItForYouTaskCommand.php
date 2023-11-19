<?php

namespace App\Command;

use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Endroid\QrCode\QrCode;

class ItForYouTaskCommand extends Command
{
    private MailerInterface $mailer;
    private string $dataPath;

    protected function configure()
    {
        parent::configure();
        $this->setName('it_for_you_task');
    }

    public function setMailer(Mailer $mailer): void
    {
        $this->mailer = $mailer;
    }

    public function setDataPath(string $dataPath)
    {
        $this->dataPath = $dataPath;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Zadanie rekrutacyjne');

            $output->writeln('Pobranie danych przy pomocy biblioteki guzle');
            $client = new Client();
            $response = $client->request('GET', 'https://v2.it4u.company/sapi/site/random_email');
            $content = $response->getBody()->getContents();
            $content = json_decode($content, true);
            $output->writeln('OK');
            $output->writeln('Adresat wiadomości: ' . $content['email']);
            $output->writeln('Temat wiadomości: ' . $content['subject']);
            $output->writeln('Treść wiadomości: ' . $content['body']);
            $output->writeln('Wartość do zakodowania: ' . $content['qr_content']);


            $output->writeln('Kodowanie QR');
            $writer = new PngWriter();
            $qrCode = QrCode::create($content['qr_content']);
            $result = $writer->write($qrCode);
            if (!file_exists($this->dataPath . '/qrCodes')) {
                mkdir($this->dataPath . '/qrCodes', 0777, true);
            }
            $filePath = $this->dataPath .'/qrCodes/qrCode' . uniqid() . '.png';
            $result->saveToFile( $filePath);
            $output->writeln('OK');

            $output->writeln('Wysyłanie wiadomości');
            $email = (new Email())
                ->from('j.trochowski@googlemail.com')
                ->to($content['email'])
                ->subject($content['subject'])
                ->text($content['body'])
                ->attachFromPath($filePath);

            $this->mailer->send($email);
            $output->writeln('OK');
        } catch (\Exception $exception) {
            $output->writeln('Nastąpił błąd podczas działania skryptu');
            $output->writeln($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}