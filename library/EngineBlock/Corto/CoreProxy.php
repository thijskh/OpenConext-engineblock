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

class EngineBlock_Corto_CoreProxy extends Corto_ProxyServer
{
    protected $_headers = array();
    protected $_output;

    protected $_voContext = null;
    
    const VO_CONTEXT_KEY          = 'voContext';
    
    
    protected $_serviceToControllerMapping = array(
        'singleSignOnService'       => 'authentication/idp/single-sign-on',
        'continueToIdP'             => 'authentication/idp/process-wayf',
        'assertionConsumerService'  => 'authentication/sp/consume-assertion',
        'continueToSP'              => 'authentication/sp/process-consent',
        'idPMetadataService'        => 'authentication/idp/metadata',
        'sPMetadataService'         => 'authentication/sp/metadata',
        'provideConsentService'     => 'authentication/idp/provide-consent',
        'processConsentService'     => 'authentication/idp/process-consent',
        'processedAssertionConsumerService' => 'authentication/proxy/processed-assertion'
    );

    public function getParametersFromUrl($url)
    {
        $parameters = array(
            'EntityCode'        => 'main',
            'ServiceName'       => '',
            'RemoteIdPMd5Hash'  => '',
        );
        $urlPath = parse_url($url, PHP_URL_PATH); // /authentication/x/ServiceName[/remoteIdPMd5Hash]
        if ($urlPath[0] === '/') {
            $urlPath = substr($urlPath, 1);
        }

        foreach ($this->_serviceToControllerMapping as $serviceName => $controllerUri) {
            if (strstr($urlPath, $controllerUri)) {
                $urlPath = str_replace($controllerUri, $serviceName, $urlPath);
                list($parameters['ServiceName'], $parameters['RemoteIdPMd5Hash']) = explode('/', $urlPath);
                return $parameters;
            }
        }

        throw new Corto_ProxyServer_Exception("Unable to map URL '$url' to EngineBlock URL");
    }

    protected function _createBaseResponse($request)
    {
        if (isset($request['__'][EngineBlock_Corto_CoreProxy::VO_CONTEXT_KEY])) {
            $vo = $request['__'][EngineBlock_Corto_CoreProxy::VO_CONTEXT_KEY];
            $this->setVirtualOrganisationContext($vo);
        }
        
        return parent::_createBaseResponse($request);
    }
    
    public function getHostedEntityUrl($entityCode, $serviceName = "", $remoteEntityId = "")
    {
        
        if (!isset($this->_serviceToControllerMapping[$serviceName])) {
            return parent::getHostedEntityUrl($entityCode, $serviceName, $remoteEntityId);
        }

        $scheme = 'http';
        if (isset($_SERVER['HTTPS'])) {
            $scheme = 'https';
        }

        $host = $_SERVER['HTTP_HOST'];

        $mappedUri = $this->_serviceToControllerMapping[$serviceName] .
            ($this->_voContext!=null && $serviceName != "sPMetadataService" ? '/' . "vo:".$this->_voContext : '') .
            ($remoteEntityId ? '/' . md5($remoteEntityId) : '');
                    
        return $scheme . '://' . $host . ($this->_hostedPath ? $this->_hostedPath : '') . $mappedUri;
    }

    public function setVirtualOrganisationContext($voContext)
    {
        $this->_voContext = $voContext;
    }
    
    public function getVirtualOrganisationContext()
    {
        return $this->_voContext;
    }
  
    
    public function getOutput()
    {
        return $this->_output;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function sendOutput($rawOutput)
    {
        $this->_output = $rawOutput;
    }

    public function sendHeader($name, $value)
    {
        $this->_headers[$name] = $value;
    }
}