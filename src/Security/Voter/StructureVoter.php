<?php
/**
 *
 * part-db version 0.1
 * Copyright (C) 2005 Christoph Lechner
 * http://www.cl-projects.de/
 *
 * part-db version 0.2+
 * Copyright (C) 2009 K. Jacobs and others (see authors.php)
 * http://code.google.com/p/part-db/
 *
 * Part-DB Version 0.4+
 * Copyright (C) 2016 - 2019 Jan Böhmer
 * https://github.com/jbtronics
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
 *
 */

namespace App\Security\Voter;


use App\Entity\AttachmentType;
use App\Entity\Category;
use App\Entity\Device;
use App\Entity\Footprint;
use App\Entity\Manufacturer;
use App\Entity\Storelocation;
use App\Entity\Supplier;
use App\Entity\User;

class StructureVoter extends ExtendedVoter
{

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        $permission_name = $this->instanceToPermissionName($subject);
        //If permission name is null, then the subject is not supported
        return ($permission_name !== null) && $this->resolver->isValidOperation($permission_name, $attribute);

    }

    /**
     * Maps a instance type to the permission name.
     * @param $subject mixed The subject for which the permission name should be generated.
     * @return string|null The name of the permission for the subject's type or null, if the subject is not supported.
     */
    protected function instanceToPermissionName($subject) : ?string
    {
        $class_name = get_class($subject);
        switch ($class_name) {
            case AttachmentType::class:
                return 'attachment_types';
            case Category::class:
                return 'categories';
            case Device::class:
                return 'devices';
            case Footprint::class:
                return 'footprints';
            case Manufacturer::class:
                return 'manufacturers';
            case Storelocation::class:
                return 'storelocations';
            case Supplier::class:
                return 'suppliers';
        }
        //When the class is not supported by this class return null
        return null;
    }

    /**
     * Similar to voteOnAttribute, but checking for the anonymous user is already done.
     * The current user (or the anonymous user) is passed by $user.
     *
     * @param $attribute
     * @param $subject
     * @param User $user
     *
     * @return bool
     */
    protected function voteOnUser($attribute, $subject, User $user): bool
    {
        $permission_name = $this->instanceToPermissionName($subject);
        //Just resolve the permission
        return $this->resolver->inherit($user, $permission_name, $attribute) ?? false;
    }


}