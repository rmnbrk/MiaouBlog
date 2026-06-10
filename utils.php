<?php
	function echoMessageErreur(?string $msg = null): void {
		echo $msg ? "<span>$msg</span>" : '';
	}

	function upload(array $fichiers, string $cheminDossier): array {
		$fichiersUpload = [];

		$cheminDossier = "uploads/$cheminDossier";

		if (!is_dir($cheminDossier)) {
			mkdir($cheminDossier, 0777, true);
		}

		foreach ($fichiers['name'] as $cle => $nom) {
			if ($fichiers['error'][$cle] === UPLOAD_ERR_OK) {
				$nomTemp = $fichiers['tmp_name'][$cle];
				$type = mime_content_type($nomTemp);

				$autorise = ['image/jpeg', 'image/png'];
				if (!in_array($type, $autorise)) continue;

				$ext = pathinfo($nom, PATHINFO_EXTENSION);
				$nomSecurise = uniqid('img_', true) . '.' . strtolower($ext);

				if (move_uploaded_file($nomTemp, "$cheminDossier/$nomSecurise")) {
					$fichiersUpload[] = $nomSecurise;
				}
			}
		}

		return $fichiersUpload;
	}

	class Commentaire {
		private $id;
		private $idAuteur;
		private $auteur;
		private $contenu;

		public function __construct(int $id, int $idAuteur, string $auteur, string $contenu) {
			$this->id = $id;
			$this->idAuteur = $idAuteur;
			$this->auteur = $auteur;
			$this->contenu = $contenu;
		}

		public function getId() : int {
        	return $this->id;
    	}

		public function getIdAuteur() : int {
        	return $this->idAuteur;
    	}

		public function getAuteur() : string {
        	return $this->auteur;
    	}

		public function getContenu() : string {
        	return $this->contenu;
    	}
	}

	class Post {
		private int $id;

		private string $titre;
		private string $description;
		private string $date;

		private string $auteur;
		private int $auteurId;

		private array $categories;

		public function __construct(int $id, string $titre, string $description, string $date, string $auteur, string $auteurId, array $categories) {
			$this->id = $id;

			$this->titre = $titre;
			$this->description = $description;
			$this->date = $date;

			$this->auteur = $auteur;
			$this->auteurId = $auteurId;

			$this->categories = $categories;
		}

		public function getId() : int {
        	return $this->id;
    	}

		public function getTitre() : string {
        	return $this->titre;
    	}

		public function getDescription() : string {
        	return $this->description;
    	}

		public function getDate() : string {
        	return $this->date;
    	}

		public function getAuteur() : string {
        	return $this->auteur;
    	}

		public function getAuteurId() : string {
        	return $this->auteurId;
    	}

		public function getCategories() : array {
        	return $this->categories;
    	}
	}
?>