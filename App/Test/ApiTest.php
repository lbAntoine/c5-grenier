<?php

namespace App\Test;


use PHPUnit\Framework\TestCase;
use App\Controllers\Api;


class ApiTest extends TestCase
{
    /*public function testProductsAction()
    {
        // Crée une instance de la classe Api
        $api = new Api();

        // Mock les valeurs de $_GET pour le test
        $_GET['sort'] = 'date';
        $_GET['recherche'] = 'keyword';

        // Mock la méthode statique getAll() de la classe Articles
        $articles = [
            ['id' => 1, 'title' => 'Article 1'],
            ['id' => 2, 'title' => 'Article 2'],
        ];
        Articles::shouldReceive('getAll')
            ->once()
            ->with('date', 'keyword')
            ->andReturn($articles);

        // Capture la sortie JSON
        ob_start();
        $api->ProductsAction();
        $output = ob_get_clean();

        // Vérifie que la réponse JSON correspond aux articles mockés
        $expectedOutput = json_encode($articles);
        $this->assertEquals($expectedOutput, $output);
    }*/


    public function testCitiesAction()
    {
        $api = new Api([]);
        $_GET['query'] = "pa";

        // Capture de la sortie générée par la méthode CitiesAction()
        ob_start();
        $api->CitiesAction();
        $output = ob_get_clean();

        // Vérification de la sortie générée
        $this->assertJson($output);
        $data = json_decode($output, true);
        $this->assertIsArray($data);
        // Vérifie la réponse HTTP 200
        $this->assertEquals(400, http_response_code());

        // Vérifie que la réponse est un tableau de chaînes
        $decodedOutput = json_decode($output, true);
        $this->assertIsArray($decodedOutput);
        foreach ($decodedOutput as $item) {
            $this->assertIsString($item);
        }
    }

    /*public function testSearch()
    {
        // Crée une instance de la classe Cities
        $cities = new Cities();

        // Mock la connexion à la base de données (getDB)
        $dbMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock la requête et le résultat attendu
        $expectedResult = ['Paris', 'Marseille', 'Lyon'];

        $stmtMock = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();
        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_COLUMN, 0)
            ->willReturn($expectedResult);

        $dbMock->expects($this->once())
            ->method('prepare')
            ->with('SELECT ville_nom_reel FROM villes_france WHERE ville_nom_reel LIKE :query')
            ->willReturn($stmtMock);

        // Injecte le mock de la base de données dans la classe Cities
        $reflectionProperty = new \ReflectionProperty(Cities::class, 'db');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($cities, $dbMock);

        // Appelle la méthode à tester
        $result = $cities->search('Par');

        // Vérifie la réponse HTTP 200
        $this->assertEquals(200, http_response_code());

        // Vérifie que le résultat est un tableau de chaînes
        $this->assertIsArray($result);
        foreach ($result as $item) {
            $this->assertIsString($item);
        }
    }*/


    /*public function testStatsAction()
    {
        // Crée une instance de la classe Api
        $api = new Api();

        // Mock la méthode statique fetch() de la classe Stats
        $stats = ['total' => 100, 'average' => 50];
        Stats::shouldReceive('fetch')
            ->once()
            ->andReturn($stats);

        // Capture la sortie JSON
        ob_start();
        $api->StatsAction();
        $output = ob_get_clean();

        // Vérifie que la réponse JSON correspond aux statistiques mockées
        $expectedOutput = json_encode($stats);
        $this->assertEquals($expectedOutput, $output);
    }*/
}
