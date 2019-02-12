<?php
/**
 * run
 * vendor/bin/simple-phpunit src/ASK/MenuBundle/Tests/Controller/MenuControllerTest.php
 */
namespace ASK\MenuBundle\Tests\Controller;

use ASK\MenuBundle\Controller\MenuController;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuControllerTest extends WebTestCase
{

    /** @var  Application $application */
    protected static $application;

    /** @var  Client $client */
    protected $client;

    /** @var  ContainerInterface $container */
    protected $container;

    /** @var  EntityManager $entityManager */
    protected $entityManager;

    public function setUp()
    {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');

        parent::setUp();
    }

    
    public function testCompleteScenario()
    {

        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/menu/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/menu/");
        $crawler = $this-> saveMenuItems($client,  $crawler, 1, 3);

        $url = '/admin/menu/?sub-menu=1';
        // Create a new entry in the database
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET $url");
        $crawler = $this-> saveMenuItems($client,  $crawler, 2, 3);
       
    }


  /*
    public function testCreateSubmenu()
    {

        $client = static::createClient();
        print 111;
        $url = '/admin/menu/?sub-menu=1';
        // Create a new entry in the database
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET $url");
        $crawler = $this-> saveMenuItems($client,  $crawler, 2, 3);
       
    }*/

    /**
     * Функция создает несколько элементов меню одного уровня
     * @param $client
     * @param $crawler
     * @param $level
     * @param int $count
     */
    protected function saveMenuItems($client,  $crawler, $level, $count = 3)
    {
        $crawlerReturn = null;
        for ($i = 1; $i <= $count; $i++) {
            $title = "Test Menu $level.$i.";
            $url = "test-menu-$level-$i";
            $crawler = $this->saveMenu($client,  $crawler, $title, $url);
            $crawler = $this->clickByLink('#back-to-list', $client, $crawler);
        }
        
        return $crawler;
    }

    /**
     * Тестируем создание меню первого уровня
     */
    protected function saveMenu($client, $crawler, $name, $url)
    {        
        /**
         * Находим ссылку, для перехода на страницу формы добавления меню, кликаем на нее и переходим на страницу формы
         */

        $crawler = $this->clickByLink('#create-menu-item', $client, $crawler);
     
        /**
         * Заполняем форму. Добавляем название меню и ссылку
         */
        $form = $crawler->filter('.menu-form')->form();
        $form->setValues([
            'ask_menubundle_menu[title]' => $name,
            'ask_menubundle_menu[url]' => $url,
        ]);

        /**
         * Отправляем  форму
         */
        $client->submit($form);
        $crawler = $client->followRedirect();

        /**
         * После сохранения, переходим на старицу show, на которой, выводится сообщение об успешном сохранения.
         * Пытаемся найти это сообщение.
         */
        $testText = '.flash-notice:contains("' . MenuController::MESSAGE_MENU_SAVED . ' '.$name.'")';
        $this->assertGreaterThan(0, $crawler->filter($testText)->count(), $testText);
        
        return $crawler;
    }
    
    protected function clickByLink($id, $client, $crawler)
    {
        print "$id \n";
        try {
            $link = $crawler->filter($id)->link();    
        } catch (\Exception $e) {
            var_dump(
                [
                    $id,
                    $crawler->filter('h1')->text(),
                    $e->getMessage()
                ]
                
            ); die;
        }       
        
        return $client->click($link);
    }
    

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);
        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }
    
}
