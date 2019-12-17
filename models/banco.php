<?php
class MongoHelper
{
    public $URI = 'mongodb://heroku_23x9xstl:fbv156gdenqi4b7hls9gducn6v@ds353338.mlab.com:53338/heroku_23x9xstl?retryWrites=false';
    public $nome = 'heroku_23x9xstl';
    public $TABELA_USUARIOS = 'USUARIOS';
    public $TABELA_GRUPOS = 'GRUPOS';
    function db()
    {
        return new MongoDB\Driver\Manager($this->URI);
    }
    function colecao($collection_name)
    {
        return new MongoDB\Collection($this->db(), $this->nome, $collection_name);
    }
}

class Usuario
{
    public $nome;
    public $email;
    public $grupos;
    public $localizacao;

    function __construct()
    {
        $this->nome = "";
        $this->email = "";
        $this->grupos = [];
        $this->localizacao = ['lat' => '', 'lon' => ''];
    }

    function __construct1($jsonString)
    {
        $json = json_decode($jsonString);
        $this->nome = ($json['nome'] == null) ? '' : $json['nome'];
        $this->email = ($json['email'] == null) ? '' : $json['email'];
        $this->grupos = ($json['grupos'] == null) ? [] : $json['grupos'];
        $this->localizacao = ($json['localizacao'] == null) ? ['lat' => '', 'lon' => ''] : $json['localizacao'];
    }

    function toJSON()
    {
        return [
            'nome' => $this->nome,
            'email' => $this->email,
            'grupos' => $this->grupos,
            'localizacao' => $this->localizacao
        ];
    }
}

class UsuariosBD
{
    function insert(Usuario $usuario)
    {
        $banco = new MongoHelper();
        $usuarios = $banco->colecao($banco->TABELA_USUARIOS);
        $insertOneResult = $usuarios->insertOne($usuario->toJSON());
        if ($insertOneResult->getInsertedCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    function getUsuario($email)
    {
        $banco = new MongoHelper();
        $usuarios = $banco->colecao($banco->TABELA_USUARIOS);
        $u = json_encode($usuarios->findOne(['email'=>$email]));
        error_log($u);
        $usuario = new Usuario($u);
        return $usuario;
    }
    function alterarLocalizacao($email, $lat, $lon)
    {
        $banco = new MongoHelper();
        $usuarios = $banco->colecao($banco->TABELA_USUARIOS);
        return $usuarios->findOneAndUpdate(
            ['email'=>$email],[ '$set' => [ 'localizacao' => ['lat' => $lat, 'lon' => $lon] ]],[]
        );
    }
}

class Grupo
{
    public $nome;
    public $senha;
    public $usuarios;

    function __construct()
    {
        $this->nome = "";
        $this->senha = "";
        $this->usuarios = [];
    }

    function __construct1(ArrayObject $json)
    {
        $this->nome = ($json['nome'] == null) ? '' : $json['nome'];
        $this->senha = ($json['senha'] == null) ? '' : $json['senha'];
        $this->usuarios = ($json['usuarios'] == null) ? [] : $json['usuarios'];
    }

    function toJSON()
    {
        return [
            'nome' => $this->nome,
            'senha' => $this->senha,
            'usuarios' => $this->usuarios
        ];
    }
}

class GruposBD
{
    function insert(Usuario $usuario)
    {
        $banco = new MongoHelper();
        $grupos = $banco->colecao($banco->TABELA_GRUPOS);
        $insertOneResult = $grupos->insertOne($usuario->toJSON());
        if ($insertOneResult->getInsertedCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    function getGrupo($nome)
    {
        $banco = new MongoHelper();
        $grupos = $banco->colecao($banco->TABELA_GRUPOS);
        $grupo = new Grupo($grupos->findOne(['nome'=>$nome]));
        return $grupo;
    }
}
