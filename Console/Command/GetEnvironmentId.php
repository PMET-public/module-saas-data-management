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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for getting store ulr by store id.
 */
class GetEnvironmentId extends Command
{
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
        $this->setName('coretech:dataspace:environment-id')
            ->setDescription(
                'Returns the environment id assigned to the instance. If none is assigned, it will return null'
            );
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $dataSpace = $this->dataSpaceRepository->get();
            $output->writeln($dataSpace->getEnvironmentId());
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Cli::RETURN_FAILURE;
        }
    }
}
