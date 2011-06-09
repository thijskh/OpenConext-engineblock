<?php
/**
 * SURFconext EngineBlock
 *
 * LICENSE
 *
 * Copyright 2011 SURFnet bv, The Netherlands
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the License.
 *
 * @category  SURFconext EngineBlock
 * @package
 * @copyright Copyright © 2010-2011 SURFnet SURFnet bv, The Netherlands (http://www.surfnet.nl)
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

interface EngineBlock_AttributeProvider_Interface
{
    /**
     * Retrieve the identifier of the attribute provider
     * @return String The URN that identifies this AttributeProvider.
     */
    public function getIdentifier();

    /**
     * Retrieve all attributes that the AttributeProvider provides for the
     * given user.
     * @param String $uid The URN of a user, for example
     *                    urn:collab:surfnet.nl:niels
     * @return Array An array containing attributes. The keys of the array are
     *               the names of the attributes. Each array element contains
     *               an array with the following elements:
     *               - format: the format of the attribute
     *               - value: the value of the attribute
     *               - source (optional): the URN of the provider of the
     *                 attribute. If source is not present, the current
     *                 AttributeProvider is the source (@see getIdentifier()).
     */
    public function getAttributes($uid);
}