<?php
require '../vendor/autoload.php';
require '../models/banco.php';
header('Access-Control-Allow-Origin: *');

$funcao = $_GET['funcao'];
switch ($funcao) {
    case 'sync_data':
        $json = json_decode(file_get_contents('php://input'), true);
        $email = $json['email'];
        $latitude = $json['lat'];
        $longitude = $json['lon'];

        $BDusuarios = (new UsuariosBD());
        $BDgrupos = (new GruposBD());

        $BDusuarios->alterarLocalizacao($email,$latitude,$longitude);
        $usuario = $BDusuarios->getUsuario($email);
        $grupos = [];
        foreach($usuario->grupos as $grupo){
            $grupo = $BDgrupos->getGrupo($grupo);
            $participantes = [];
            foreach($grupo->usuarios as $participante){
                $u = $BDusuarios->getUsuario($participante);
                $participantes[] = [
                    'nome'=>$u->nome,
                    'localizacao'=>$u->localizacao
                ];
            }
            $grupos[] = [$grupo->nome => $participantes];
        }
        echo json_encode(['error'=>false, 'grupos' => $grupos]); 
    break;
}
?>