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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for getting store ulr by store id.
 */
class Configure extends Command
{
    /**
     * Name of input arguments.
     */
    private const INPUT_ENVIRONMENT_ID = 'environment-id';
    private const INPUT_PRODUCTION_PRIVATE_KEY = 'production-private-key';
    private const INPUT_PRODUCTION_PUBLIC_KEY = 'production-public-key';
    private const INPUT_SANDBOX_PRIVATE_KEY = 'sandbox-private-key';
    private const INPUT_SANDBOX_PUBLIC_KEY = 'sandbox-public-key';
    private const INPUT_ENVIRONMENT_NAME = 'environment-name';
    private const INPUT_PROJECT_NAME = 'project-name';
    private const INPUT_PROJECT_ID = 'project-id';

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
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('coretech:dataspace:configure')
            ->setDescription(
                'Sets the configuration of the Commerce Services Connector'
            )
            ->setDefinition($this->getOptionsList());
        parent::configure();
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        try {
            $dataSpace = $this->dataSpaceRepository->get();
            //check that env id is passed
            if (empty($input->getOption(self::INPUT_ENVIRONMENT_ID))) {
                throw new \InvalidArgumentException('--environment-id is required');
                return Cli::RETURN_FAILURE;
            }
            
            //check that env id is valid uuid
            if (!$this->isUUID($input->getOption(self::INPUT_ENVIRONMENT_ID))) {
                throw new \InvalidArgumentException('--environment-id is not a valid UUID');
                return Cli::RETURN_FAILURE;
            }
            $dataSpace->setEnvironmentId($input->getOption(self::INPUT_ENVIRONMENT_ID));
            
            //if any of the keys are passed, then we are setting the keys
            if (!empty($input->getOption(self::INPUT_PRODUCTION_PRIVATE_KEY))) {
                $dataSpace->setProductionPrivateKey($input->getOption(self::INPUT_PRODUCTION_PRIVATE_KEY));
            } else {
                //if we are missing a key, then return an error
                if (empty($dataSpace->getProductionPrivateKey())) {
                    throw new \InvalidArgumentException('--production-private-key is required');
                    return Cli::RETURN_FAILURE;
                }
            }
            if (!empty($input->getOption(self::INPUT_PRODUCTION_PUBLIC_KEY))) {
                $dataSpace->setProductionPublicKey($input->getOption(self::INPUT_PRODUCTION_PUBLIC_KEY));
            } else {
                //if we are missing a key, then return an error
                if (empty($dataSpace->getProductionPublicKey())) {
                    throw new \InvalidArgumentException('--production-public-key is required');
                    return Cli::RETURN_FAILURE;
                }
            }

            if (!empty($input->getOption(self::INPUT_SANDBOX_PRIVATE_KEY))) {
                $dataSpace->setSandboxPrivateKey($input->getOption(self::INPUT_SANDBOX_PRIVATE_KEY));
            } else {
                //if we are missing a key, then return an error
                if (empty($dataSpace->getSandboxPrivateKey())) {
                    throw new \InvalidArgumentException('--sandbox-private-key is required');
                    return Cli::RETURN_FAILURE;
                }
            }

            if (!empty($input->getOption(self::INPUT_SANDBOX_PUBLIC_KEY))) {
                $dataSpace->setSandboxPublicKey($input->getOption(self::INPUT_SANDBOX_PUBLIC_KEY));
            } else {
                //if we are missing a key, then return an error
                if (empty($dataSpace->getSandboxPublicKey())) {
                    throw new \InvalidArgumentException('--sandbox-public-key is required');
                    return Cli::RETURN_FAILURE;
                }
            }

            if (!empty($input->getOption(self::INPUT_ENVIRONMENT_NAME))) {
                $dataSpace->setEnvironmentName($input->getOption(self::INPUT_ENVIRONMENT_NAME));
            } else {
                //if we are missing a key, then return an error
                if (empty($dataSpace->getEnvironmentName())) {
                    throw new \InvalidArgumentException('--environment-name is required');
                    return Cli::RETURN_FAILURE;
                }
            }

            if (!empty($input->getOption(self::INPUT_PROJECT_NAME))) {
                $dataSpace->setProjectName($input->getOption(self::INPUT_PROJECT_NAME));
            } else {
                //if we are missing a key, then return an error
                if (empty($dataSpace->getProjectName())) {
                    throw new \InvalidArgumentException('--project-name is required');
                    return Cli::RETURN_FAILURE;
                }
            }

            if (!empty($input->getOption(self::INPUT_PROJECT_ID))) {
                $dataSpace->setProjectId($input->getOption(self::INPUT_PROJECT_ID));
            } else {
                //if we are missing a key, then return an error
                if (empty($dataSpace->getProjectId())) {
                    throw new \InvalidArgumentException('--project-id is required');
                    return Cli::RETURN_FAILURE;
                }
            }

            //validate?
            $this->dataSpaceRepository->save($dataSpace);
            $output->writeln('Data Space Configuration saved');
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
                self::INPUT_ENVIRONMENT_ID,
                'environment-id',
                InputOption::VALUE_REQUIRED,
                'Environment Id (Data Space Id) (required)'
            ),
            new InputOption(
                self::INPUT_PRODUCTION_PRIVATE_KEY,
                'production-private-key',
                InputOption::VALUE_REQUIRED,
                'Production Private Key'
            ),
            new InputOption(
                self::INPUT_PRODUCTION_PUBLIC_KEY,
                'production-public-key',
                InputOption::VALUE_REQUIRED,
                'Production Public Key'
            ),
            new InputOption(
                self::INPUT_SANDBOX_PRIVATE_KEY,
                'sandbox-private-key',
                InputOption::VALUE_REQUIRED,
                'Sandbox Private Key'
            ),
            new InputOption(
                self::INPUT_SANDBOX_PUBLIC_KEY,
                'sandbox-public-key',
                InputOption::VALUE_REQUIRED,
                'Sandbox Public Key'
            ),
            new InputOption(
                self::INPUT_ENVIRONMENT_NAME,
                'environment-name',
                InputOption::VALUE_REQUIRED,
                'Environment Name'
            ),
            new InputOption(
                self::INPUT_PROJECT_NAME,
                'project-name',
                InputOption::VALUE_REQUIRED,
                'Project Name'
            ),
            new InputOption(
                self::INPUT_PROJECT_ID,
                'project-id',
                InputOption::VALUE_REQUIRED,
                'Project Id'
            ),

        ];
    }
    /**
     * Check if string is a valid uuid
     *
     * @param string $uuid
     * @return int|false
     */
    private function isUUID($uuid)
    {
        return preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid);
    }
}
