<?php


namespace SuperDock\Service;

use Exception;
use Symfony\Component\HttpClient\HttpClient;

class redmineService
{

	static function getProjectList() : Array
	{

		$DATA = [ 'no projet id: #false' ];

        try {
        
            $httpClient = HttpClient::create();

            $projects = $httpClient->request( 'GET', 'http://sensiogrey.easyredmine.com/projects.xml?include=&offset=0&limit=25&page=', [
                'verify_peer' => false,
                'headers' => [
                    'Accept' => 'application/xml',
                    'Authorization' => 'Basic xxxxxxxxxxxx',
                ]
            ]);
            $xml = simplexml_load_string( $projects->getContent(), "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);

            $DATA = [];
            foreach ( $array['project'] as $project  ) {
                $DATA[] = $project['name'] . ' id: #' . $project['id'];
            }
            
        } catch (Exception $e) {
 
        }

		return $DATA;
		
	}
}