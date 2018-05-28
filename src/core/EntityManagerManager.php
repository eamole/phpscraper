<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 28/05/2018
 * Time: 01:59
 */

namespace Core;
/*
 * this tracks all EntityManagers, which track all entities
 *
 */




class EntityManagerManager extends Base{

	public static $entityClasses=[];

	public static function getEntityManager($entityClass){
		// TODO : no guard
		return self::$entityClasses[$entityClass];
	}

	public static function registerEntityManager($entityManager) {
		if (!self::isEntityClassRegistered($entityManager->entityClass)) {
			self::$entityClasses[$entityManager->entityClass]=$entityManager;
		}
	}
	public static function isRegisteredEntityClass($entityClass) {
		return isset(self::$entityClasses[$entityClass]);
	}


}