<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('itaro_sitemaps')} (
      `sitemap_id` int(10) unsigned NOT NULL auto_increment,
      `filename` varchar(250) NOT NULL default '',
      `path` varchar(250) default NULL,
      `link` varchar(250) default NULL,
      `store_id` smallint(5) unsigned NULL,
      `type` int(10) unsigned default NULL,
      `created_at` datetime default NULL,
      `updated_at` datetime default NULL,  
      PRIMARY KEY  (`sitemap_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS itaro_sitemap_types (
      `type_id` int(10) unsigned NOT NULL auto_increment,
      `name` varchar(80) NOT NULL default '',
      `fields` varchar(5000) NOT NULL default '',
      PRIMARY KEY  (`type_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");
 
$installer->endSetup();