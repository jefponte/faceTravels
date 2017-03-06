<?php



class MapaDAO extends DAO
{
	public function DAO(PDO $conexao = null){
		if($conexao != null){
			$this->conexao = $conexao;
		}else{
			$this->fazerConexao();
	
		}
	}
	
	public function inserir(Mapa $mapa){
	
		$this->getConexao()->beginTransaction();
		$sql = "INSERT INTO mapa(titulo)
				VALUES(:titulo)";
		
		$titul = $mapa->getTitulo();
		$idUsuario = $mapa->getDono()->getId();
		
		try {
			$db = $this->getConexao();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("titulo", $titul, PDO::PARAM_STR);
			$result = $stmt->execute();
			if(!$result){
				$this->getConexao()->rollBack();
				return false;
				
			}
			
			
	
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
		$idMapa = $this->getConexao()->lastInsertId();
		
		$sql = "ISNERT INTO usuario_mapa(id_usuario, id_mapa) 
				VALUES(:idUsuario, :idMapa)";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam("idUsuario", $idUsuario, PDO::PARAM_STR);
			$stmt->bindParam("idMapa", $idMapa, PDO::PARAM_STR);
			$result = $stmt->execute();
			if(!$result){
				$this->getConexao()->rollBack();
				return false;
		
			}	
		
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
		return true;
	
	}
	
	public function retornaLista() {
		$lista = array ();
		$sql = "SELECT * FROM mapa
				INNER JOIN usuario_mapa 
				ON usuario_mapa.id_mapa = mapa.id_mapa
				LIMIT 1000";
		$result = $this->getConexao ()->query ( $sql );
	
		foreach ( $result as $linha ) {
			$mapa = new Mapa();
			$mapa->setId($linha['id_mapa']);
			$mapa->setTitulo($linha['titulo']);
			
			$lista [] = $mapa;
		}
		return $lista;
	}
	
}

?>