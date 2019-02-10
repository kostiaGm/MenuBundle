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

        $this-> saveMenuItems($client,  $crawler, 1, 3);
        
        /**
         * Создаем меню первого уровня
         */
      /*  $title = 'menu 1.1';
        $url = 'menu-1-1';
        $crawler = $this->saveMenu($client,  $crawler, $title, $url);*/

        /**
         * Переходим на страницу списка меню самого верхнего уровня
         */
//        $crawler = $this->clickByLink('#back-to-list', $client, $crawler);

        /**
         * Переходим на страницу списка подменю
         */
  /*      $crawler = $this->clickByLink('a.sub-menu', $client, $crawler);
        
        dump($crawler->filter('h1')->text());
        
        die;*/

        /* foreach ($crawler->filter('.flash-notice') as $item) {
             var_dump($item->textContent);    
         }
         
         
         
          
         die;*/
        // dump($messageSuccess);


        /*  $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
  
          // Fill in the form and submit it
          $form = $crawler->selectButton('Create')->form(array(
              'ask_menubundle_menu[field_name]'  => 'Test',
              // ... other fields to fill
          ));
  
          $client->submit($form);
          $crawler = $client->followRedirect();
  
          // Check data in the show view
          $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');
  
          // Edit the entity
          $crawler = $client->click($crawler->selectLink('Edit')->link());
  
          $form = $crawler->selectButton('Update')->form(array(
              'ask_menubundle_menu[field_name]'  => 'Foo',
              // ... other fields to fill
          ));
  
          $client->submit($form);
          $crawler = $client->followRedirect();
  
          // Check the element contains an attribute with value equals "Foo"
          $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');
  
          // Delete the entity
          $client->submit($crawler->selectButton('Delete')->form());
          $crawler = $client->followRedirect();
  
          // Check the entity has been delete on the list
          $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());*/
    }

    /**
     * Функция создает несколько элементов меню одного уровня
     * @param $client
     * @param $crawler
     * @param $level
     * @param int $count
     */
    protected function saveMenuItems($client,  $crawler, $level, $count = 3)
    {
        for ($i = 1; $i <= $count; $i++) {
            $title = "Test Menu $level.$i.";
            $url = "test-menu-$level-$i";
            $crawler = $this->saveMenu($client,  $crawler, $title, $url);
            $crawler = $this->clickByLink('#back-to-list', $client, $crawler);
        }
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
        $link = $crawler->filter($id)->link();
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
