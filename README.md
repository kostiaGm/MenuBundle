# MenuBundle
MenuBundle By Symfony3

Это простой бандл. В версии 1.1.1 релаизует такой функционал:

1. Управление меню. Создавать/Редактировать/Удалять/Менять положение элементов меню
2. Вывод меню на фронтенд-части

Установка:

1. Создайте проект Symfony 3
2. Запустите команду composer require kot/menu-bundle
4. Добавьте в файл app/config/AppKernel.php ссылку на бандл
<pre>
$bundles = [
    // ...
        new ASK\MenuBundle\MenuBundle(),
    // ...
];
</pre>
5. Установите css framework bootstrap 4. Например, в файле app/Resources/views/default/base.html.twig
 <pre>
 ....
    &lt;link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous"&gt;
         &lt;link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" /&gt;
         &lt;link href="https://unpkg.com/ionicons@4.5.5/dist/css/ionicons.min.css" rel="stylesheet"&gt;
    &lt;/head&gt;

  &lt;body&gt;
   ....
      &lt;script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"&gt;&lt;/script&gt;
      &lt;script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"&gt;&lt;/script&gt;
      &lt;script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"&gt;&lt;/script&gt;
    &lt;/body&gt;


 </pre>

