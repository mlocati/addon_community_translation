<?php
namespace CommunityTranslation\Tests\Api;

use CommunityTranslation\Tests\Helper\ApiClient;
use PHPUnit\Framework\TestCase;
use Concrete\Core\Support\Facade\Application;

abstract class ApiTest extends TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    private static $app;

    private static $apiRootURL;

    /**
     * @var \Concrete\Core\Config\Repository\Liaison
     */
    protected static $config;

    /**
     * @var ApiClient|null
     */
    protected $apiClient;

    public static function setUpBeforeClass()
    {
        self::$app = Application::getFacadeApplication();
        self::$apiRootURL = 'http://localhost:49150';
        self::$config = self::$app->make('community_translation/config');
    }

    protected function setUp()
    {
        $this->apiClient = new ApiClient(self::$apiRootURL . self::$config->get('options.api.entryPoint'));
    }
}
