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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for getting store ulr by store id.
 */
class ClearConfigure extends Command
{
    /**
     * Name of input argument.
     */
    private const INPUT_ARGUMENT_ALL = 'all';

    /**
     * @var DataSpaceRepositoryInterface
     */
    private $dataSpaceRepository;

    /**
     * @var array
     */
    private $optionsList;

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
        $this->setName('coretech:dataspace:clear-config')
            ->setDescription(
                'Unassign the dataspace from the instance. --all option will also remove api keys'
            )
            ->setDefinition($this->getOptionsList());

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
            $dataSpace->setEnvironmentId('');
            $dataSpace->setEnvironmentName('');
            $dataSpace->setProjectId('');
            $dataSpace->setProjectName('');
            $dataSpace->setImsOrgId('');
            if ($input->getOption(self::INPUT_ARGUMENT_ALL)) {
                $dataSpace->setSandboxPublicKey('');
                $dataSpace->setProductionPublicKey('');
                $dataSpace->setSandboxPrivateKey('');
                $dataSpace->setProductionPrivateKey('');
            }
            $this->dataSpaceRepository->save($dataSpace);
            $this->$output->writeln('Data Space Configuration cleared');
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * Create options for command
     *
     * @return InputOption[]
     */
    private function getOptionsList()
    {
        return [
            new InputOption(
                self::INPUT_ARGUMENT_ALL,
                'all',
                InputOption::VALUE_NONE,
                'Clear all Commerce Connector Settings'
            )
        ];
    }
}
