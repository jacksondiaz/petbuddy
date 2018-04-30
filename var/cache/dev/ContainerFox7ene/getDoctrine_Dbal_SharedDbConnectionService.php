<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'doctrine.dbal.shared_db_connection' shared service.

include_once $this->targetDirs[3].'/vendor/doctrine/dbal/lib/Doctrine/DBAL/Logging/LoggerChain.php';
include_once $this->targetDirs[3].'/vendor/doctrine/dbal/lib/Doctrine/DBAL/Configuration.php';
include_once $this->targetDirs[3].'/vendor/doctrine/common/lib/Doctrine/Common/EventManager.php';
include_once $this->targetDirs[3].'/vendor/symfony/symfony/src/Symfony/Bridge/Doctrine/ContainerAwareEventManager.php';
include_once $this->targetDirs[3].'/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/Connection.php';
include_once $this->targetDirs[3].'/vendor/doctrine/dbal/lib/Doctrine/DBAL/Connection.php';
include_once $this->targetDirs[3].'/vendor/doctrine/orm/lib/Doctrine/ORM/Tools/AttachEntityListenersListener.php';
include_once $this->targetDirs[3].'/vendor/doctrine/doctrine-bundle/ConnectionFactory.php';

$a = new \Doctrine\DBAL\Logging\LoggerChain();
$a->addLogger(${($_ = isset($this->services['doctrine.dbal.logger']) ? $this->services['doctrine.dbal.logger'] : $this->load('getDoctrine_Dbal_LoggerService.php')) && false ?: '_'});
$a->addLogger(${($_ = isset($this->services['doctrine.dbal.logger.profiling.shared_db']) ? $this->services['doctrine.dbal.logger.profiling.shared_db'] : $this->services['doctrine.dbal.logger.profiling.shared_db'] = new \Doctrine\DBAL\Logging\DebugStack()) && false ?: '_'});

$b = new \Doctrine\DBAL\Configuration();
$b->setSQLLogger($a);

$c = new \Symfony\Bridge\Doctrine\ContainerAwareEventManager($this);
$c->addEventListener(array(0 => 'loadClassMetadata'), ${($_ = isset($this->services['doctrine.orm.shared_db_listeners.attach_entity_listeners']) ? $this->services['doctrine.orm.shared_db_listeners.attach_entity_listeners'] : $this->services['doctrine.orm.shared_db_listeners.attach_entity_listeners'] = new \Doctrine\ORM\Tools\AttachEntityListenersListener()) && false ?: '_'});

return $this->services['doctrine.dbal.shared_db_connection'] = ${($_ = isset($this->services['doctrine.dbal.connection_factory']) ? $this->services['doctrine.dbal.connection_factory'] : $this->services['doctrine.dbal.connection_factory'] = new \Doctrine\Bundle\DoctrineBundle\ConnectionFactory(array())) && false ?: '_'}->createConnection(array('driver' => 'pdo_mysql', 'host' => 'localhost', 'port' => 3306, 'dbname' => 'sharedDB', 'user' => 'root', 'password' => 'root', 'charset' => 'UTF8', 'unix_socket' => ($this->targetDirs[5].'/tmp/mysql/mysql.sock'), 'driverOptions' => array(), 'defaultTableOptions' => array()), $b, $c, array());
