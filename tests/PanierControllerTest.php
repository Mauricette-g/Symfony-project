<?php


namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testAddProductToCartAndCheckout()
    {
        $client = static::createClient();
        $client = static::createClient();

        // Se connecter avec un utilisateur existant (fixtures ou création manuelle)
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'usernameTest',
            'email' => 'usertest@com',
            'delivery_adress' => 'adresse test',
            'password' => 'passwordTest'
        ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.navbar', 'Utilisateur connecté');


        // Ajouter un produit au panier (POST)
        $client->request('POST', '/panier/ajouter/41', ['taille' => 'M']);
        $this->assertResponseRedirects('/panier');

        // Vérifier que la page panier affiche le produit
        $client->followRedirect();
        $this->assertSelectorTextContains('td', 'Blackbelt**');
        
        
        // Simuler le paiement
        $client->disableReboot();
        $client->getContainer()->set('stripe_client_mock', new class {
            public function createCheckoutSession() {
                return (object)['url' => '/paiement-simule'];
            }
        });




    }
}
