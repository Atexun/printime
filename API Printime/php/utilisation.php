<?php 

include 'printime.class.php';

/*
** Remplacer ces valeurs par celles qui vous sont fournies.
** $u_t : User (sous forme de mail)
** $k_t : Clé d'API
*/

$u_t = "";
$k_t = "";

/*
** Coordonées du recepteur
*/

$adresse = array
	(
		"denomination-recepteur" => "Mr",
		"nom-recepteur" => "Antoine",
		"prenom-recepteur" => "Dupond",
		"adresse-recepteur" => "13 rue Françoise Bechet",
		"adresse-2-recepteur" => "Appartement 3",
		"code-postal-recepteur" => "49800",
		"ville-recepteur" => "Trélazé",
		"type-enveloppe" => "118-sans-fenetre",
		"type-courrier" => "Eco+",
		"suivis-postal" => false,
        "pays-recepteur" => "FR",
	);

$infos_impression_document = array
	(
    	'est-recto-verso' => false,
		'est-en-couleur' => false,
		'est-en-papier-ameliore' => false,
	);

$commande = new Printime($u_t, $k_t);

$solde = $commande->getSolde();
print_r($solde);

$nouvelle_commande = $commande->createCmd($adresse);
print_r($nouvelle_commande);

$ajout_document = $commande->chargementDocument("document.pdf", $infos_impression_document);
print_r($ajout_document);

$confirmation = $commande->confirmCmd();
print_r($confirmation);

$solde = $commande->getSolde();
print_r($solde);

echo "\n";
