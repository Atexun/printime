<?php

/*
** Implémentation PHP de l'API de d'impression et d'envoi de courrier en ligne via le service Printime.
** https://printime.fr
** Par Antoine Habert - Atexun
** 21 juillet 2014
*/

class Printime {
	private $u;
	private $k;
	private $base_url = "https://printime.fr/api/";
	private $id_cmd ='';

	/*
	** On set le $u (user) et le $k (clé API)
	*/
	function __construct($u_t, $k_t) {
		$this->u = $u_t;
		$this->k = $k_t;
	}
	
	private function setIdCmd($id_cmd) {
		$this->id_cmd = $id_cmd;
	}

	/*
	** Envoi via cURL de la requête GET
	*/
	private function curlGet($url) {
		$ch = curl_init();
		$options = array(
			CURLOPT_URL            => $url,
    		CURLOPT_RETURNTRANSFER => true, // Retourner le contenu téléchargé dans une chaine (au lieu de l'afficher directement)
    		CURLOPT_HEADER         => false // Ne pas inclure l'entête de réponse du serveur dans la chaine retournée
    	);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		curl_close($ch);
		return ($result);
	}

	/*
	** Envoi via cURL de la requête POST
	*/
	private function curlPost($url, $postfield) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
		$response = curl_exec($ch);
		curl_close($ch);
		return($response);
	}

	/*
	** Formate les requêtes en Json
	*/
	private function encodeJson($adress) {
		return json_encode($adress);
	}

	/*
	** Requête en GET le solde d'une commande
	** @return string json : le solde de la commande
	*/
	public function getSolde() {
		$function_url = "solde";
		$url =  $this->base_url . $this->u . '/' . $this->k . '/' . $function_url;
		$result = $this->curlGet($url);
		$solde = json_decode($result, true);
		return ($solde);
	}

	/* 
	** Passer commande : Etape 1/3
	** Envoi de l'adresse de réception
	** @param $adress l'adresse, array PHP, de réception de la commande
	** @return string json : l'id de la commande, la date de réception estimée, et le solde.
	*/
	public function createCmd($adress) {
		$function_url = "creation";
		$url =  $this->base_url . $this->u . '/' . $this->k . '/' . $function_url;
		$result = $this->curlPost($url, $this->encodeJson($adress));
		$commande = json_decode($result, true);
		if (isset($commande["id"]))
			$this->setIdCmd($commande["id"]);
		return ($commande);
	}

	/* 
	** Passer commande : Etape 2/3
	** Ajout du document à la commande
	*/
	public function chargementDocument($filename, $infos) {
		$function_url = "chargementDocument";
		$url =  $this->base_url . $this->u . '/' . $this->k . '/' . $function_url . '/' . $this->id_cmd;
		$donnees = array
		(
			'file' => new CURLFile($filename,'application/pdf','_'),
			'infos' => $this->encodeJson($infos)
		);
		$result = $this->curlPost($url, $donnees);
		$document = json_decode($result, true);
		return ($document);
	}

	/* 
	** Passer commande : Etape 3/3
	** Confirmation finale de la commande
	** @return string json : confirmation de l'envoi de la commande (bool).
	*/
	public function confirmCmd() {
		$function_url = "confirmation";
		$url =  $this->base_url . $this->u . '/' . $this->k . '/' . $function_url . '/' . $this->id_cmd;
		$result = $this->curlPost($url, $this->id_cmd);
		$confirmation = json_decode($result, true);
		return ($confirmation);
	}
}