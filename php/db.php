<?php
	function connect_db(){		
		
		//Criar liga�ao � base de dados
		$serverDB="localhost";
		$portDB="5432";
		$nameDB="Requilibrius";
		$usernameDB="requilibriusAdmin";
		$passDB="requilibrius";
		$dbconn = pg_connect("host=".$serverDB." port=".$portDB." dbname=".$nameDB."
							  user=".$usernameDB." password=".$passDB)
				  or die('Nao foi possivel estabelecer ligacao com: ' . pg_last_error());
		return $dbconn;
	}
	
	function get_destaque($dbconn){
		
		$query = "SELECT * FROM destaque";
		$query_response = pg_query($dbconn,$query);
		$counter = 0;
		$destaques = [];
		while($row = pg_fetch_array ($query_response,$counter,PGSQL_BOTH))
		{
			$destaques[$counter]['id'] = $row['id'];
			$destaques[$counter]['titulo'] = $row['titulo'];
			$destaques[$counter]['resumo'] = $row['resumo'];
			$destaques[$counter] ['texto'] = $row['texto'];
			//$destaques[$counter]['data_in'] = $row['data_in'];
			
			//get associated images
			$destaques[$counter]['img'] = get_img($dbconn, 'destaque', $destaques[$counter]['id']);
			
			$counter++;//proxima medicao da tabela SQL
			if($counter==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}
		return $destaques;
	}
	
	function get_formacao($dbconn){
		$query = "SELECT * FROM formacao";
		$query_response = pg_query($dbconn,$query);
		$counter = 0;
		$formacao = [];
		while($row = pg_fetch_array ($query_response,$counter,PGSQL_BOTH))
		{
			$formacao[$counter]['id'] = $row['id'];
			$formacao[$counter]['titulo'] = $row['titulo'];
			$formacao[$counter]['resumo'] = $row['resumo'];
			$formacao[$counter] ['texto'] = $row['texto'];
			$formacao[$counter]['data_in'] = $row['data_in'];
			
			//get associated images
			$formacao[$counter]['img'] = get_img($dbconn, 'formacao', $formacao[$counter]['id']);
			
			$counter++;//proxima medicao da tabela SQL
			if($counter==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}
		
		return $formacao;
	}
	
	function get_tecnica($dbconn){
        
        $query = "SELECT * FROM tecnica";
		$query_response = pg_query($dbconn,$query);
		$counter = 0;
		$tecnica = [];
		while($row = @pg_fetch_array ($query_response,$counter,PGSQL_BOTH))
		{
			$tecnica[$counter]['id'] = $row['id'];
			$tecnica[$counter]['nome'] = $row['nome'];
			
			//get associated images
			$tecnica[$counter]['img'] = get_img($dbconn, 'tecnica', $tecnica[$counter]['id']);
			$tecnica[$counter]['detalhes'] = get_detalhes($dbconn, $tecnica[$counter]['id']);
			
			$counter++;//proxima medicao da tabela SQL
			if($counter==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}
		
		return $tecnica;
	}
	
	function get_equipa($dbconn){
		$query = "SELECT * FROM funcionario";
		$query_response = pg_query($dbconn,$query);
		$counter = 0;
		$equipa = [];
		while($row = pg_fetch_array ($query_response,$counter,PGSQL_BOTH))
		{
			$equipa[$counter]['id'] = $row['id'];
			$equipa[$counter]['nome'] = $row['nome'];
			$equipa[$counter]['equipa'] = $row['equipa'];			
			//get associated images 
			$equipa[$counter]['img'] = get_img($dbconn, 'funcionario', $equipa[$counter]['id']);
			//get associated CV itemsn
			$equipa[$counter]['cv'] = get_cvitem($dbconn, $equipa[$counter]['id']);//wrong name 'cv'
			
			$counter++;//proxima medicao da tabela SQL
			if($counter==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}
		echo json_encode($equipa);
		return $equipa;
	}
	
	function get_cvitem($dbconn, $func_id){
        
        $query = "SELECT * FROM cvitem WHERE funcionario_id = '".$func_id."' ORDER BY seq asc;";
		$query_response = pg_query($dbconn,$query);
		$counter = -1;//couner++ antes de adicionar primeiro
		$rowNbr = 0;
		$cv = [];
		
		//ALTERAR
		$current_field = '';
		while($row = @pg_fetch_array ($query_response,$rowNbr,PGSQL_BOTH))
		{
			if( $current_field != $row['field']){
				$current_field = $row['field'];
				$counter++;//
				$cv[$counter]['field'] = $row['field'];
				$cv[$counter]['content'] = [];
			}
			array_push($cv[$counter]['content'],$row['content']);	
			$rowNbr++;
			if($rowNbr==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}
		return $cv;
	}
	
	function get_section($dbconn, $pagina){
        
        $query = "SELECT * FROM section WHERE pagina = '".$pagina."'";
		$query_response = pg_query($dbconn,$query);
		$counter = 0;
		$section = [];
		while($row = @pg_fetch_array ($query_response,$counter,PGSQL_BOTH))
		{
			$section[$counter]['id'] = $row['id'];
			$section[$counter]['alt_id'] = $row['alt_id'];
			$section[$counter]['nome'] = $row['nome'];
			$section[$counter]['text'] = $row['texto'];
			
			//get associated images
			$section[$counter]['img'] = get_img($dbconn, 'section', $tecnica[$counter]['id']);
			
			$counter++;//proxima medicao da tabela SQL
			if($counter==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}
		return $section;
	}
	
	function get_img($dbconn, $entidade, $entidade_id){
	    $query = "SELECT * FROM img WHERE entidade = '".$entidade."' AND entidade_id = ".$entidade_id.";";
        
		$query_response = pg_query($dbconn,$query);
		$counter = 0;
		$img = [];
		while($row = @pg_fetch_array ($query_response,$counter,PGSQL_BOTH))
		{
			$img[$counter]['path'] = $row['path'];
			$img[$counter]['nome'] = $row['nome'];
			$img[$counter]['id'] = $row['id'];
			$img[$counter]['descricao'] = $row['descricao'];
			$counter++;//proxima medicao da tabela SQL
			if($counter==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}	
		return $img;
	}

    function get_detalhes($dbconn, $entidade_id){
	    $query = "SELECT * FROM tecnicaDetalhe WHERE entidade_id = ".$entidade_id.";";
        
		$query_response = pg_query($dbconn,$query);
		$counter = 0;
		$detalhes = [];
		while($row = @pg_fetch_array ($query_response,$counter,PGSQL_BOTH))
		{
			$detalhes[$counter]['id'] = $row['id'];
			$detalhes[$counter]['detalhe'] = $row['item'];
			$counter++;//proxima medicao da tabela SQL
			if($counter==pg_num_rows($query_response)){
				break;	//para a execu��o do ciclo para que n�o haja erro quando $counter>numero de linhas na tabela
			}
		}
		return $detalhes;
	}

    function save_contact($dbconn, $email, $nome, $apelido, $telefone, $descricao, $motivo){
        
        $query = "INSERT INTO contact(
						email, nome, apelido, telefone, descricao, motivo)
						VALUES ('".$email."', '".$nome."', '".$apelido."', '".$telefone."', '".$descricao."', '".$motivo."');";
		echo "<br>query:<br>".$query."<br>";
		$query_response = pg_query($dbconn,$query);
		if (!$query_response) {
		 echo "\nErro a inserir dados.<br>";
		}else{
			echo "Contacto inseridos<br>";
		}
    }
	
	

?>