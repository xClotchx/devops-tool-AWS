<?php
class Script {
    private $conn;
    private $table_name = "scripts";

    public $id;
    public $user_id; // Nueva propiedad fundamental para aislar los datos
    public $title;
    public $description;
    public $code_content;
    public $instructions;
    public $category_id;
    public $category_name;
    public $image_path; 

    public function __construct($db) {
        $this->conn = $db;
    }

    // OBTENER TODOS LOS SCRIPTS FILTRADOS POR USUARIO Y CATEGORÍA
    // OBTENER TODOS LOS SCRIPTS FILTRADOS POR USUARIO Y CATEGORÍA (CORREGIDO CON LEFT JOIN)
    public function readAll($category_id = null) {
        // Cambiamos JOIN por LEFT JOIN para que no oculte los scripts si falla la categoría
        $query = "SELECT s.id, s.title, s.description, s.code_content, s.instructions, s.image_path, s.created_at, c.name as category_name
                  FROM " . $this->table_name . " s
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE s.user_id = :user_id";
        
        if ($category_id !== null && $category_id > 0) {
            $query .= " AND s.category_id = :category_id";
        }
        
        $query .= " ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        
        if ($category_id !== null && $category_id > 0) {
            $stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // CREAR SCRIPT (Asociando automáticamente tu user_id)
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, title=:title, description=:description, code_content=:code_content, instructions=:instructions, category_id=:category_id, image_path=:image_path";

        $stmt = $this->conn->prepare($query);

        $this->user_id = intval($this->user_id);
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->code_content = $this->code_content; 
        $this->instructions = $this->instructions;
        $this->category_id = intval($this->category_id);
        $this->image_path = $this->image_path;

        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":code_content", $this->code_content);
        $stmt->bindParam(":instructions", $this->instructions);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image_path", $this->image_path);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // OBTENER UN SOLO SCRIPT POR ID (Validando pertenencia para blindar la URL)
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ACTUALIZAR SCRIPT (Blindado por ID y User ID)
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title=:title, description=:description, code_content=:code_content, instructions=:instructions, category_id=:category_id, image_path=:image_path 
                  WHERE id=:id AND user_id=:user_id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = intval($this->id);
        $this->user_id = intval($this->user_id);
        $this->category_id = intval($this->category_id);
        $this->image_path = $this->image_path;

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":code_content", $this->code_content);
        $stmt->bindParam(":instructions", $this->instructions);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ELIMINAR SCRIPT (Solo si te pertenece)
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>