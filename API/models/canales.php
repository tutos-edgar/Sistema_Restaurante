
<?php

require_once '../models/FuncionesGenerales.php';
date_default_timezone_set('America/Guatemala');

class Canales
{
    private $conn;
    private $table = "canales";
    private $tableVideo = "videos";
    private $tableVideoShort = "videos_short";
    private $general;
     public $id_canal, $nombre_canal, $url_canal, $descripcion_canal;
     private $tablaConsultarDatos;
   
    public function __construct($db)
    {
        $this->conn = $db; 
        $this->general = new FuncionesGenerales();        
    }

    public function obtenerTodosMisCanales()
    {
        try{
            $query = "SELECT * FROM " . $this->table;
            $stmt  = $this->conn->query($query);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            return $datos;
        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function obtenerTodos()
    {
        try{
            $query = "SELECT * FROM " . $this->table;
            $stmt  = $this->conn->query($query);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // foreach ($datos as &$fila) {
            //     $idproveedor = $fila['id_proveedor'];
            //     $rol = "ADMINISTRADOR";

            //     if(strtoupper($rol) == "ADMINISTRADOR"){
            //         $fila['botones'] = '
            //             <div class="text-center">
            //                 <div class="btn-group">
            //                     <button data-id="' . $idproveedor . '" class="btn btn-primary btn-sm btnEditar">
            //                         <i class="material-icons"></i> Editar
            //                     </button>
            //                     <button data-id="' . $idproveedor . '" class="btn btn-danger btn-sm btnEliminar">
            //                         <i class="material-icons"></i> Eliminar
            //                     </button>
            //                 </div>
            //             </div>
            //         ';
            //     }else{
            //         $fila['botones'] = '<span class="badge bg-secondary">Sin Rol</span>';
            //     }
            
            //     // Puedes seguir agregando más campos personalizados si necesitas
            // }

            return $datos;

        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function MisVideosRamdom()
    {
        try{
            
            $misCanales = $this->obtenerTodos();
            if(isset($misCanales['success']) && $misCanales['success'] === false){
                if(isset($misCanales['error']) && $misCanales['error'] === true){
                    if(isset($misCanales['mensaje'])){
                        return ["success" => false, "error" => true,  "mensaje" => $misCanales['mensaje']];
                    }
                }
                if(isset($misCanales['mensaje'])){
                    return ["success" => false,  "mensaje" => $misCanales['mensaje']];
                }
            }
            $cantidadCanales = count($misCanales);
            $canalAleatorio = rand(0, $cantidadCanales -1);
            do {
                $canalAleatorio = rand(0, $cantidadCanales -1);
            } while($canalAleatorio < 0);            
    
            $videoSeleccionado = $this->obtenerVideosRamdomPorCanal($misCanales[$canalAleatorio]['id_canal']);
            if(isset($videoSeleccionado) || !empty($videoSeleccionado)){
                return ["success" => true,  "datos" =>$videoSeleccionado];
            }else{
                return ["success" => false,  "error" =>false,  "mensaje" =>"Datos Vacios"];
            }
            
            

        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

       
        // $stmt  = $this->conn->prepare($query);
        // $stmt->execute([$id]);
        // $datos= $stmt->fetchAll(PDO::FETCH_ASSOC);

        // foreach ($datos as &$fila){
        //     $usuario = $fila['user_usuario'];
           
        //     $fila['user_usuario'] = $usuario;
        // }
        
        // return $datos;
    }

    public function obtenerVideosRamdomPorCanal($canal)
    {
        try{

            $videosSeleccionado = $this->seleccionarTipoVideosPorCanal($canal);
         
            if(isset($videosSeleccionado['success']) && $videosSeleccionado['success'] === false){
                if(isset($videosSeleccionado['error']) && $videosSeleccionado['error'] === true){
                    if(isset($videosSeleccionado['mensaje'])){
                    return ["success" => false, "error" => true,  "mensaje" => $videosSeleccionado['mensaje']];
                }
                }
                if(isset($videosSeleccionado['mensaje'])){
                    return ["success" => false,  "mensaje" => $videosSeleccionado['mensaje']];
                }
            }
            
            $cantidadVideos = count($videosSeleccionado);
            
            $videoAleatorio = rand(0, $cantidadVideos - 1);
           
            do {
                $videoAleatorio = rand(0, $cantidadVideos - 1);
            } while($videoAleatorio < 0);

            if(!empty($videosSeleccionado) && $cantidadVideos>0){
                //$this->actualizarConsultaVideo($videosSeleccionado[$videoAleatorio]['id_video'], $videosSeleccionado[$videoAleatorio]['consulta']);
                return ["video" =>$videosSeleccionado[$videoAleatorio]['link_video'], "tiempo" =>$videosSeleccionado[$videoAleatorio]['tiempo_video'],"idvideo" => $this->general->getYoutubeId($videosSeleccionado[$videoAleatorio]['link_video'])] ;
            }else{
                return null;
            }
           

        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    } 
    
    
    public function seleccionarTipoVideosPorCanal($canal)
    {
        try{
            $tipoVideoAleatorio = rand(0, 1);
            do {
                $tipoVideoAleatorio = rand(0, 1);
            } while($tipoVideoAleatorio < 0);

            if($tipoVideoAleatorio == 0){
                $this->tablaConsultarDatos = $this->tableVideo;
                return $this->buscarVideo($canal);
            }else{
                $this->tablaConsultarDatos = $this->tableVideoShort;
                return $this->buscarVideoShort($canal);
            }

        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    } 



    public function buscarVideoShort($canal)
    {
        try{
            $query = "SELECT * FROM " . $this->tableVideoShort." WHERE id_canal=?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$canal]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function buscarVideo($canal)
    {
        try{
            $query = "SELECT * FROM " . $this->tableVideo." WHERE id_canal=?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$canal]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }
        
    }

    public function actualizarConsultaVideo($id, $cantidad){
        $valorIngresar = $cantidad +1;
        try{
            $query = "UPDATE ".$this->tablaConsultarDatos." SET consulta=? WHERE id_video = ?";
            $stmt  = $this->conn->prepare($query);
            $stmt->execute([$valorIngresar, $id]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
        }catch (PDOException $e) {
            return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        } catch (Exception $e) {
           return ["success" => false, "error" => true, "mensaje" => $this->general->validarCodigoDeError($e->getCode(), $e->getMessage())];
        }

    }




}
