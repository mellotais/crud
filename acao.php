<?php

$acao = isset($_POST['acao'])?$_POST['acao']:'';

if ($acao =='Salvar'){

    //formata data
    $data = DateTime::createFromFormat('Y-m-d', $_POST['nascimento']);
    $datastatus = FALSE;
    if($data && $data->format('Y-m-d') === $_POST['nascimento']){
    $datastatus = TRUE;
    }   
    //validações
    if (empty($_POST["nome"])) {
        echo "Nome inválido";
    }elseif (empty($_POST["email"])) {
        echo "Email inválido";
    }elseif ($_POST["idade"] < 0 or $_POST["idade"] > 120 or ! is_numeric($_POST["idade"])) {
        echo "Idade inválida";
    }elseif ($datastatus == FALSE) {
        echo "Data inválida";
    }elseif ($_POST["parente"]!= "1" and $_POST["parente"]!= "2") {
        echo "Parentesco inválido";
    }elseif ($_POST["cidade"]!= "1" and $_POST["cidade"]!= "2"and $_POST["cidade"]!= "3") {
        echo "Local inválido";
    }else{

        //pega "ação" do link
        $acao = isset($_GET['acao'])?$_GET['acao']:'';

        //identificação 
        if($acao =='editar'){
            $id = isset($_GET['id'])?$_GET['id']:'';
        }else{
            $id = 0;    //id temporario
        }
        
        if ($id == 0){
            $dados = get_dados();
            $id = nextID($dados);   //set de id real
            $organiz = organize($id);
    
            if ($dados){ 
                array_push($dados,$organiz);
            }else{
                $dados[] = $organiz;
            }
    
            file_put_contents("contatos.json",json_encode($dados));
    
            header('location: contato.php');
        }else{
            //alterar ao invés de adicionar
            $organiz = organize($id);
            alterar($organiz);
            header('location: index.php'); 
        }
        
    }
}else {
    
    $acao = isset($_GET['acao'])?$_GET['acao']:'';
    $id = isset($_GET['id'])?$_GET['id']:'';

    //chama a função
    if ($acao == 'excluir'){
        excluir($id);
    }
}

//exclui
function excluir($id){
    $dados = get_dados();
    $i = 0;
    foreach($dados as $contato){
        if ($contato['id'] == $id)
            break;
        else
        $i++;
    }
    array_splice($dados,$i,1);
    file_put_contents("contatos.json",json_encode($dados));
}

//altera
function alterar($alterado){
    $dados = get_dados();
    $i = 0;
    foreach($dados as $contato){
        if ($contato['id'] == $alterado['id'])
            break;
        else
        $i++;
    }
    array_splice($dados,$i,1,array($alterado));
    file_put_contents("contatos.json",json_encode($dados));
}

function get_dados(){
    //pega dados do json
    $contatos[] = "";

    if (file_exists("contatos.json")){
        $conteudo =file_get_contents("contatos.json");
        $contatos = json_decode($conteudo, true);
    }

    return $contatos;
}

function organize($id){
    //formata dados do formulario
    $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data = $_POST['nascimento'];
        $idade = $_POST['idade'];
        $cidade = $_POST['cidade'];
        $parente = $_POST['parente'];
        $rede = $_POST['rede'];
        $origem = $_POST['origem'];
        $sexo = $_POST['sexo'];

        $dados = array("id"=>strval($id),
                    'Nome' => $nome,
                    'Sobrenome' => $sobrenome,
                    'Email' => $email,
                    'Telefone' => $telefone,
                    'Data' => $data,
                    'Idade' => $idade,
                    'Cidade' => $cidade,
                    'Parente' => $parente,
                    'Rede'=> $redesocial,
                    'Origem' => $origem,
                    'Sexo' => $sexo ); 
   
    return $dados;
}

function nextID($dados){
    //aplica IDs
    $id = 0;
    if ($dados)
        $id = intval($dados[count($dados)-1]['id']);
    return ++$id;
}

?>