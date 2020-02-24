<?php
declare (strict_types = 1);

namespace App\Controller\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;


/**
 * Class Chat
 * @package App\Controller\Server
 */
class Chat extends AbstractController implements MessageComponentInterface
{
    protected $users;

    protected $clients;

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        
        $this->users[$conn->resourceId] = [
            'connection' => $conn,
            'user' => '',
            'rol' => '',
        ];
        /*$conn->send(json_encode([
            'user' => "",
            'mensaje' => "Conectado usuario {$conn->resourceId}",
            ]));*/
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        unset($this->users[$closedConnection->resourceId]);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('Error ' . $e->getMessage());
        $conn->close();
    }

    /**
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $messageData = json_decode($msg);
        
        //echo($messageData);
        if(strlen($messageData->rol) > 1)
        {
            echo "registro";
            $this->users[$from->resourceId] = [
                'connection' => $from,
                'user' => $messageData->de,
                'rol' => $messageData->rol,
            ];
        }else {
            echo "mensaje";
            $numRecv = count($this->users) - 1;
            // echo sprintf('Connection %d sending message "%s" to %d other socketection%s' . "\n"
             //    , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
     
             //echo $this->users[$from->resourceId]['connection']->resourceId  . "\n" ;
              foreach ($this->users as $user) {
                
                 if ($messageData->para == $user['user']) {
                     try {
                         echo $messageData->mensaje;
                         $user['connection']->send(json_encode([
                             'mensaje' => $messageData->mensaje,
                             'fecha' => $messageData->fecha,
                             'de' => $messageData->de,
                             ]));
                     } catch (\Throwable $th) {
                         echo "fallo el envio";
                     }
                 } elseif($user['rol'] == "ROLE_ADMIN"){
                     try {
                         $user['connection']->send(json_encode([
                             'nuevo' => "si",
                             ]));
                     } catch (\Throwable $th) {
                         echo "fallo el nuevo envio";
                     }
                 }
                 
             } 
        }
        
    }
    
}
