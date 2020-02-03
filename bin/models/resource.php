<?php

use spitfire\Model;
use spitfire\storage\database\Schema;

/**
 * The resource describes a theoretical box which is locked, the grant represents
 * the key a user can use to unlock the box.
 * 
 * The resource is just represented by an identifier. Which is called key (as in
 * key value pairs), when an application whishes to check whether a user can perform
 * a certain task on it it will send a resource identifier together with the user's
 * identities.
 * 
 * Our system will then recursively walk down the keys and check whether the user can
 * perform the action.
 * 
 * @property ResourceModel $parent The parent node
 * @property string $key The key for searching this resource
 * @property bool $record Whether changes to the resource and it's subtree should be logged
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class ResourceModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 * @return Schema
	 */
	public function definitions(Schema $schema) {
		$schema->parent = new Reference(ResourceModel::class);
		$schema->key = new StringField(255);
		
		/*
		 * If this flag is set, the system will record changes made to the subtree
		 * of this resource to the log. This allows the application to trace the 
		 * behavior of users and applications on sensitive entries.
		 * 
		 * You may not want to log that a user did block another user, but you may
		 * want to record that an administrator changed a sensitive key or that an
		 * application may have been compromised to changing data it should not.
		 */
		$schema->record = new BooleanField();
		$schema->removed = new IntegerField(true);
		
		$schema->index($schema->parent, $schema->key);
	}
	
	public function unlock($identity) {
		$db = $this->getTable()->getDb();
		
	}
	
}