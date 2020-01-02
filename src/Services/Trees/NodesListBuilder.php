<?php
/**
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace App\Services\Trees;

use App\Entity\Base\DBElement;
use App\Entity\Base\NamedDBElement;
use App\Entity\Base\StructuralDBElement;
use App\Helpers\Trees\TreeViewNode;
use App\Repository\StructuralDBElementRepository;
use App\Services\EntityURLGenerator;
use App\Services\UserCacheKeyGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *  This service gives you a flat list containing all structured entities in the order of the structure.
 */
class NodesListBuilder
{
    protected $em;
    protected $cache;
    protected $keyGenerator;

    public function __construct( EntityManagerInterface $em, TagAwareCacheInterface $treeCache, UserCacheKeyGenerator $keyGenerator)
    {
        $this->em = $em;
        $this->keyGenerator = $keyGenerator;
        $this->cache = $treeCache;
    }

    /**
     * Gets a flattened hierachical tree. Useful for generating option lists.
     * In difference to the Repository Function, the results here are cached.
     *
     * @param string                   $class_name The class name of the entity you want to retrieve.
     * @param StructuralDBElement|null $parent     This entity will be used as root element. Set to null, to use global root
     *
     * @return StructuralDBElement[] A flattened list containing the tree elements.
     */
    public function typeToNodesList(string $class_name, ?StructuralDBElement $parent = null): array
    {
        $parent_id = null != $parent ? $parent->getID() : '0';
        // Backslashes are not allowed in cache keys
        $secure_class_name = str_replace('\\', '_', $class_name);
        $key = 'list_'.$this->keyGenerator->generateKey().'_'.$secure_class_name.$parent_id;

        $ret = $this->cache->get($key, function (ItemInterface $item) use ($class_name, $parent, $secure_class_name) {
            // Invalidate when groups, a element with the class or the user changes
            $item->tag(['groups', 'tree_list', $this->keyGenerator->generateKey(), $secure_class_name]);
            /**
             * @var StructuralDBElementRepository
             */
            $repo = $this->em->getRepository($class_name);

            return $repo->toNodesList($parent);
        });

        return $ret;
    }
}
