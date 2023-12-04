<?php

namespace App\Command;

use App\Entity\TaxNumberFormat;
use App\Enum\Country as EnumCountry;
use App\Infrastructure\Service\Filesystem\FileManager;
use App\Infrastructure\Service\Filesystem\Reader\FileReaderInterface;
use App\Infrastructure\Service\Tax\FormatToRegConverter\Exception\ConvertException;
use App\Infrastructure\Service\Tax\FormatToRegConverter\FormatToRegConverter;
use App\Repository\CountryRepository;
use App\Repository\TaxNumberFormatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'load:tax-number:format',
)]
class LoadTaxNumberFormatCommand extends Command
{
    public const DATA_FILE = 'tax_number_format.json';

    public function __construct(
        private readonly FileManager $fileManager,
        private readonly FileReaderInterface $fileReader,
        private readonly EntityManagerInterface $em,
        private readonly CountryRepository $countryRepository,
        private readonly TaxNumberFormatRepository $taxNumberFormatRepository,
        private readonly FormatToRegConverter $formatToRegConverter
    ) {
        parent::__construct();
    }

    /**
     * @throws ConvertException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filePath = $this->fileManager->makeFullDataFilePath(self::DATA_FILE);
        $formats = $this->fileReader->read($filePath);

        $persistCounter = 0;
        foreach ($formats as $formatItem) {
            $countryName = $formatItem['country'];
            $formatString = $formatItem['format'];

            $countryEntity = $this->countryRepository->findOneBy(['name' => $countryName]);

            if (null === $countryEntity) {
                $io->note(sprintf('Страны %s нет', $countryName));
                continue;
            }

            $pattern = $this->formatToRegConverter->convert($formatString, EnumCountry::from($countryName));
            $newFormat = $this->taxNumberFormatRepository->findOneBy(['country' => $countryEntity]);
            if (null === $newFormat) {
                $newFormat = new TaxNumberFormat($formatString, $pattern, $countryEntity);
            }
            $newFormat->setPattern($pattern);
            $newFormat->setFormat($formatString);

            $this->em->persist($newFormat);

            ++$persistCounter;
            if ($persistCounter > 100) {
                $this->em->flush();
                $this->em->clear();
                $persistCounter = 0;
            }
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}
