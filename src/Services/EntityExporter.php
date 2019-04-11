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

namespace App\Services;


use App\Entity\NamedDBElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Use this class to export an entity to multiple file formats.
 * @package App\Services
 */
class EntityExporter
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Exports an Entity or an array of entities to multiple file formats.
     *
     * @param $entity NamedDBElement|NamedDBElement[] The element/elements[] that should be exported
     * @param Request $request The request that should be used for option resolving.
     * @return Response The generated response containing the exported data.
     * @throws \ReflectionException
     */
    public function exportEntityFromRequest($entity, Request $request) : Response
    {
        $format = $request->get('format') ?? "json";

        //Check if we have one of the supported formats
        if (!in_array($format, ['json', 'csv', 'yaml', 'xml'])) {
            throw new \InvalidArgumentException("Given format is not supported!");
        }

        //Check export verbosity level
        $level = $request->get('level') ?? 'extended';
        if (!in_array($level, ['simple', 'extended', 'full'])) {
            throw new \InvalidArgumentException('Given level is not supported!');
        }

        //Check for include children option
        $include_children = $request->get('include_children') ?? false;

        //Check which groups we need to export, based on level and include_children
        $groups = array($level);
        if ($include_children) {
            $groups[] = 'include_children';
        }

        //Plain text should work for all types
        $content_type = "text/plain";

        //Try to use better content types based on the format
        switch ($format) {
            case 'xml':
                $content_type = "application/xml";
                break;
            case 'json':
                $content_type = "application/json";
                break;
        }

        $response = new Response($this->serializer->serialize($entity, $format,
            [
                'groups' => $groups,
                'as_collection' => true,
                'csv_delimiter' => ';', //Better for Excel
                'xml_root_node_name' => 'PartDBExport'
            ]));

        $response->headers->set('Content-Type', $content_type);

        //If view option is not specified, then download the file.
        if (!$request->get('view')) {
            if ($entity instanceof NamedDBElement) {
                $entity_name = $entity->getName();
            } elseif (is_array($entity)) {
                if (empty($entity)) {
                    throw new \InvalidArgumentException('$entity must not be empty!');
                }

                //Use the class name of the first element for the filename
                $reflection = new \ReflectionClass($entity[0]);
                $entity_name = $reflection->getShortName();
            } else {
                throw new \InvalidArgumentException('$entity type is not supported!');
            }


            $filename = "export_" . $entity_name . "_" . $level . "." . $format;

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );
            // Set the content disposition
            $response->headers->set('Content-Disposition', $disposition);
        }

        return $response;
    }
}