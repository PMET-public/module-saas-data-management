<?php
/**
 * Copyright 2023 Adobe, Inc. All rights reserved.
 * See LICENSE for license details.
 */

/** Usage

 **/

namespace MagentoEse\SaasDataManagement\Console\Command;

use Magento\Framework\Console\Cli;
use MagentoEse\SaasDataManagement\Api\Data\DataSpaceInterface;
use MagentoEse\SaasDataManagement\Api\DataSpaceRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for getting store ulr by store id.
 */
class ClearCatalog extends Command
{
    /**
     * Name of input argument.
     */
    private const INPUT_ARGUMENT_ENVIRONMENT_ID = 'environment-id';

    /**
     * @var DataSpaceRepositoryInterface
     */
    private $dataSpaceRepository;

    /**
     * @param DataSpaceRepositoryInterface $dataSpaceRepository
     */
    public function __construct(
        DataSpaceRepositoryInterface $dataSpaceRepository
    ) {
        parent::__construct();
        $this->dataSpaceRepository = $dataSpaceRepository;
    }

    /**
     * Configure
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('coretech:dataspace:clear-catalog')
            ->setDescription(
                'Clears the catalog data in the dataspace of the given id. Will use the id 
                assigned to the instance if none is passed'
            );
        $this->addArgument(
            self::INPUT_ARGUMENT_ENVIRONMENT_ID,
            InputArgument::OPTIONAL,
            'Data Space ID'
        );

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $dataSpace = $this->dataSpaceRepository->get();
            $environmentId = $input->getArgument(self::INPUT_ARGUMENT_ENVIRONMENT_ID);
            if ($environmentId !== null) {
                $dataSpace->setEnvironmentId($environmentId);
            }if ($dataSpace->getEnvironmentId() === null) {
                throw new InvalidArgumentException('No Data Space ID provided or assigned');
            }
            $result = $dataSpace->clearCatalog($dataSpace);
            $output->writeln('Data Space ID: ' . $environmentId .' cleared');
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Cli::RETURN_FAILURE;
        }
    }
}
