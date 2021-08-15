<?php

class Database {
    private $mysqli;

    private $statement_result;

    /**
     * @throws Exception
     */
    public function __construct(string $db_host, string $db_user, string $db_pass, string $db_name) {
        $this->mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

        if($this->mysqli->connect_errno) {
            throw new Exception("DB Error - connection failed: " . $this->mysqli->connect_error);
        }
    }

    /**
     * @throws Exception
     */
    public function query(string $query, string $param_types = null, array $params = null, bool $return_results = false) {

        if(!$stmt = $this->mysqli->prepare($query)) {
            throw new Exception("DB Error - Cannot prepare query: " . $this->mysqli->error);
        }

        if(!empty($param_types) && !empty($params)) {
            $bind_params[] = $param_types;

            for ($i = 0; $i < count($params); $i++) {
                $bind_params[] = &$params[$i]; //mysqli bind_param requires reference instead of values.
            }
            call_user_func_array(array($stmt, 'bind_param'), $bind_params);
        }

        if(!$stmt->execute()) {
            throw new Exception("DB Error - Query execution failed: " . $stmt->error);
        }

        $this->statement_result = $stmt;

        if($return_results) {
            return $stmt;
        }
    }

    public function getStatementResult() {
        return $this->statement_result;
    }

    /**
     * @throws Exception
     */
    public function select(string $query, string $param_types = null, array $params = null) {
        try {
            $stmt = $this->query($query, $param_types, $params, true);
            return $stmt->get_result();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function select_one(string $query, string $param_types = null, array $params = null) {
        try {
            $result = $this->select($query, $param_types, $params);
            return $result->fetch_assoc();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function select_many(string $query, string $param_types = null, array $params = null) {
        try {
            $stmt = $this->select($query, $param_types, $params);
            $result = [];
            while($row = $stmt->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function __destruct() {
        $this->mysqli->close();
    }


}