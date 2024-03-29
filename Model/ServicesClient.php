<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MagentoEse\SaasDataManagement\Model;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\ServicesConnector\Api\ClientResolverInterface;
use Magento\ServicesConnector\Exception\KeyNotFoundException;
use Magento\ServicesConnector\Api\KeyValidationInterface;
use Magento\ServicesConnector\Exception\PrivateKeySignException;
use Psr\Log\LoggerInterface;

/**
 * @inheritDoc
 */
class ServicesClient implements ServicesClientInterface
{
    /**
     * Config paths
     */
    private const ROUTE_CONFIG_PATH = 'services_connector_magentoese/services_id/registry_api_path';
    private const ENVIRONMENT_CONFIG_PATH = 'magento_saas/environment';

    /**
     * Extension name for Services Connector
     */
    private const EXTENSION_NAME = 'MagentoEse_SaasDataManagement';

    /**
     * @var ClientResolverInterface
     */
    private $clientResolver;

    /**
     * @var KeyValidationInterface
     */
    private $keyValidator;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ClientResolverInterface $clientResolver
     * @param KeyValidationInterface $keyValidator
     * @param ScopeConfigInterface $config
     * @param Json $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientResolverInterface $clientResolver,
        KeyValidationInterface $keyValidator,
        ScopeConfigInterface $config,
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->clientResolver = $clientResolver;
        $this->keyValidator = $keyValidator;
        $this->config = $config;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function request(string $method, string $uri, string $data = ''): array
    {
        $result = [];
        $environment = $this->config->getValue(self::ENVIRONMENT_CONFIG_PATH);
        try {
            $client = $this->clientResolver->createHttpClient(
                self::EXTENSION_NAME,
                $environment
            );
            $headers = ['Content-Type' => 'application/json'];
            $options = [
                'headers' => $headers,
                'body' => $data
            ];

            if ($this->validateApiKey()) {
                $response = $client->request($method, $uri, $options);
                $result = $this->serializer->unserialize($response->getBody()->getContents());
                if (isset($result['message']) && $result['message'] == 'JWT is invalid') {
                    $message = __(
                        'You have entered a '
                        . $environment
                        . ' private key that does not match the corresponding API key. '
                        . 'Check that the private key is correct.'
                    );
                    $result = [
                        'status' => 403,
                        'statusText' => 'FORBIDDEN',
                        'message' => $message
                    ];
                }
            } else {
                $errorText = (string) __('Your API Key is invalid.');
                $linkUrl = 'https://devdocs.magento.com/recommendations/configure.html#apikeys';
                $linkText = (string) __('Learn More');
                $result = [
                    'status' => 403,
                    'statusText' => 'FORBIDDEN',
                    'message' => $this->buildErrorMessage($errorText, $linkUrl, $linkText)
                ];
                $this->logger->error(__('API Key Validation Failed'));
            }
        } catch (KeyNotFoundException $ex) {
            $message = __('Magento API Key not found');
            $result = [
                'status' => 403,
                'statusText' => 'FORBIDDEN',
                'message' => $message
            ];
            $this->logger->error($ex->getMessage());
        } catch (PrivateKeySignException $ex) {
            $message = __(
                'You have entered an invalid '
                . $environment
                . ' private key. Check that the private key is correct.'
            );
            $result = [
                'status' => 500,
                'statusText' => 'INTERNAL_SERVER_ERROR',
                'message' => $message
            ];
            $this->logger->error($ex->getMessage());
        } catch (GuzzleException | InvalidArgumentException $ex) {
            $errorText = (string) __('Something went wrong. Check your connection and try again.');
            $linkUrl = 'https://devdocs.magento.com/guides/v2.3/config-guide/saas/environment.html';
            $linkText = (string) __('Learn More');
            $result = [
                'status' => 500,
                'statusText' => 'INTERNAL_SERVER_ERROR',
                'message' => $this->buildErrorMessage($errorText, $linkUrl, $linkText)
            ];
            $this->logger->error(self::EXTENSION_NAME . ': ' . __('An error occurred contacting Magento Services'));
            $this->logger->error($ex->getMessage());
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(string $version, string $uri) : string
    {
        $route = $this->config->getValue(self::ROUTE_CONFIG_PATH);
        $url = sprintf('/%s/%s/%s', $route, $version, $uri);
        return $url;
    }

    /**
     * Validate the API Gateway Key
     *
     * @return bool
     * @throws KeyNotFoundException
     * @throws InvalidArgumentException
     */
    private function validateApiKey() : bool
    {
        return $this->keyValidator->execute(
            self::EXTENSION_NAME,
            $this->config->getValue(self::ENVIRONMENT_CONFIG_PATH)
        );
    }

    /**
     * Get error message for display to user
     *
     * @param string $errorText
     * @param string $linkUrl
     * @param string $linkText
     * @return string
     */
    private function buildErrorMessage(string $errorText, string $linkUrl = '', string $linkText = '') : string
    {
        $message = $errorText;
        if ($linkUrl) {
            $message .= ' <a href="' . $linkUrl . '" target="_blank" title="' . $errorText . '">';
            $message .= $linkText;
            $message .= '</a>';
        }
        return $message;
    }
}
