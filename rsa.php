<?php
include('includes/php/BigInteger.php');
error_reporting(~E_ALL);  

class Rsa {
	private $privateKey = Array();//array che conterrà la chiave privata formata da(d,n)
	private $publicKey = Array();//array che conterrà la chiave pubblica formata da(e,n)
	
	function Rsa($_privateKey,$_publicKey){
        if(is_null($_privateKey[n])&&is_null($_publicKey[n]))
            $this->generateKeys($_privateKey,$_publicKey);//ogni volta che creo l'oggetto,genero le due chiavi(privata e pubblica)
		$this->privateKey = $_privateKey;
		$this->publicKey = $_publicKey;
    }
    

    function getPrivateKey(){
        return $this->privateKey;
    }

    function getPublicKey(){
        return $this->publicKey;
    }

    function privateKeyToString(){
        return base64_encode($this->privateKey[d].'.'.$this->privateKey[n]);
    }

    function publicKeyToString(){
        return base64_encode($this->publicKey[e].'.'.$this->publicKey[n]);
    }
    

    function generateKeys(&$private_key,&$public_key){
        //variabili  usate per i calcoli
        $BigInteger = new Math_BigInteger();
        $min = new Math_BigInteger(1000000000000);
        $max = new Math_BigInteger(10000000000000);
        $p = new Math_BigInteger();
        $q = new Math_BigInteger();
        $n = new Math_BigInteger();
        $b = new Math_BigInteger();
        $e = new Math_BigInteger();
        $d = new Math_BigInteger();
        
        $p = $BigInteger -> randomPrime($min,$max,1000);//cerco un numero primo
        do{
        $q = $BigInteger -> randomPrime($min,$max,1000);//cerco un numero primo
        }while($q==$p);//ciclo finchè non trovo che i due numeri primi siano diversi tra di loro
        $n=$p ->multiply($q);//calcolo la N (N=p*q)
        $b= eulero($p,$q);//calcolo la funzione di eulero per trovare il valore di b
        $e = findMcd($b);//trovo la e,ovvero cerco il primo intero e che sia primo con b
        $d = $e->modInverse($b);//per calcolare il valore di d,utilizzo la funzione già presente nella classe BigInteger
		//compongo gli array delle chiavi con i valori relativi
        $public_key = array("e" => $e->toString(), "n" =>$n->toString());
        $private_key = array("d" => $d->toString(), "n" =>$n->toString());
    }

    function cryptMessageRSA($message,$e_string,$n_string){
		//converto i valori in variabili BigInteger
        $e = new Math_BigInteger($e_string);
        $n = new Math_BigInteger($n_string);
        $tmp = new Math_BigInteger(0);
        $asciiMessage= unpack('C*', $message);//trasformo il messaggio in un array composto dal codice ASCII di ogni singola lettera
        $res=array();
        for($i=1;$i<=count($asciiMessage);$i++){//per ogni lettera
            $char = new Math_BigInteger($asciiMessage[$i]);//creo la variabile BigInteger contente il valore della lettera
            $tmp = $char->modPow($e,$n);//eseguo la funzione per criptografare la lettera(c=(char*e)%n)
            $res[$i] =$tmp->toString();//compongo l'array del risultato con il valore ottenuto
        }
        return $res;
    }

    function decryptMessageRSA($cryptedArray,$d_string,$n_string){
		//converto i valori in variabili BigInteger
        $d = new Math_BigInteger($d_string);
        $n = new Math_BigInteger($n_string);
        $tmp = new Math_BigInteger(0);
        $res="";//inizializzo la stringa che conterrà il messaggio decriptato
		//in questo caso ricevo un array contenenti tutti i valori delle lettere criptate
		//per ogni valore:
        for($i=1;$i<=count($cryptedArray);$i++){
            $char = new Math_BigInteger($cryptedArray[$i]);//creo la variabile BigInteger contente il valore 
            $tmp = $char->modPow($d,$n);//eseguo la funzione decriptare il valore(c=(char*d)%n)
            $packedASCII = pack('C*',$tmp->toString());//trasformo il risultato(un numero) al corrsipettivo simbolo
            $res = $res.$packedASCII;//accodo alla variabile contenente il risultato,la lettera appena trovata
        }
        return $res;
    }
}


function eulero($p,$q){
	//e = (p-1)*(q-1)
	//inizializzo le variabili che userò per i calcoli
    $tmp = new Math_BigInteger();
    $val = new Math_BigInteger(1);
    $sub = new Math_BigInteger(1);
	//lo svolgo per parti
    $sub = $p->subtract($val);//la variabile temporanea sub = (p-1)
    $tmp = $sub->multiply(($q->subtract($val)));//moltiplico la variabile trovata (sub)*(q-1) 
    return $tmp;//faccio il return del risultato trovato
}


function findMcd($b) {
	//partendo da un contatore , aumento il valore di esso finchè l'MCD del contatore con b non è uguale a 1
	$n= new Math_BigInteger(2);
	$one= new Math_BigInteger(1);
    $res= new Math_BigInteger(0);
	while(!($res->equals($one))){
        $res = $n->gcd($b);//eseguo la funzione per il calcolo dell'mcd tra il contatore e la b
		$n = $n->add($one);//incrtemento il contatore di 1
	}
    $n = $n->subtract($one);//sottraggo uno in quanto il valore è stato incrementato una volta di più
	return $n;
}


?>