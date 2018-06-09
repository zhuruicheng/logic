<?php  
class logic{
	protected $pdbLink;
	protected $pdbName;
	protected $pdbPassWord;
	protected $pdbUserName;
	function __construct() {
		$this->pdbLink = "121.43.37.189";
		$this->pdbName = "trademark";
		$this->pdbPassWord = "root";
		$this->pdbUserName = "root";
	}
	public function activity($type,$sql,$sNum){
		$list = array(); 
		$conn = new mysqli($this->pdbLink,$this->pdbUserName,$this->pdbPassWord,$this->pdbName);
		//检测数据库连接    0     正常连接    1 连接失败   2 sql语句错误   3    查询结果为空     4
		$list["code"] = 0; 
		if ($conn->connect_errno) {
			$list["code"] = 1;
			$list["msg"] = "数据库连接错误";
			return $list;
		} 
		$conn->set_charset("UTF8mb4");
// 		$sql = "select * from sc_test";
// 		$sql = "insert into sc_test (name, type) values ('小明',1)";
// 		$sql = "update sc_test set name = '小虎dui' where id = 2";
// 		$sql = "delete from sc_test where id = 2"; 
		/**
		 * s  select 查询
		 * a  add    添加
		 * u  update 更新
		 * d  delete 删除
		 * c  count  统计查询条数
		 * */ 
		$result = $conn->query($sql);
		$list["sql"] = $sql;
		switch ($type){
			case "c":
				if ($result)
				{
					if ($result->num_rows>0)
					{
						return $result->num_rows;
					}else{ 
						return 0;
					}
				}else{ 
					return -1;
				}
				break;
			case "s":
				if ($result)
				{
					if ($result->num_rows>0)
					{
						$li = array();
						while ($rows = $result->fetch_array(MYSQL_ASSOC)) {
							array_push($li,$rows);
						}
						$list["code"] = 0;
						$list["list"] = $li;
						$list["allCount"] = $sNum;
						$list["count"] = $result->num_rows;
						$list["msg"] = "success";
						$li = null;
						return $list;
					}else{
						$list["code"] = 3;
						$list["allCount"] = $sNum;
						$list["count"] = 0;
						$list["msg"] = "Data is empty";
						return $list;
					}
				}else{
					$list["code"] = 2;
					$list["msg"] ="sql statement error";
					return $list;
				}
				break;
			case "a":
				if ($result) {
					$list["code"] = 0;
					$list["msg"] ="insert success";
					return $list;
				} else {
					$list["code"] = 2;
					$list["msg"] ="insert failure";
					return $list;
				}
				break;
			case "u":
				if ($result) {
					$list["code"] = 0;
					$list["msg"] ="update success";
					return $list;
				} else {
					$list["code"] = 2;
					$list["msg"] ="update failure";
					return $list;
				}
				break;
			case "d":
				if ($result) {
					$list["code"] = 0;
					$list["msg"] ="delete success";
					return $list;
				} else {
					$list["code"] = 2;
					$list["msg"] ="delete failure";
					return $list;
				}
				break;
		}
		
	}
	/**
	 * @insert     插入语句拼接
	 * @$formName  表名
	 * @$obj       条件数组
	 * */
	public function insert($formName,$obj){
		$keys = "";
		$values = "";
		foreach ($obj as $key => $value){
			if(gettype($value) == "string"){
				$value= "'".$value."'";
			}
			$keys .= ",".$key;
			$values .= ",".$value;
		} 
		$keys = substr($keys,1,strlen($keys));
		$values = substr($values,1,strlen($values));
		$sql = "insert into ".$formName." (".$keys.") values (".$values.")";
 		return $this->activity("a",$sql,0); 
	}
	/**
	 * @cselect    统计查询条数  
	 * @$formName  表名
	 * @$result    需要的字段
	 * @$obj       条件数组
	 * 
	 * */
	public function cselect($formName,$result,$obj){
		//拼接返回字段
		$results   = "";
		$condition = "";
		if(gettype($result) == "array"){
			for ($i=0; $i<count($result); $i++)
			{
				$results .= ",".$result[$i];
			}
			$results= substr($results,1,(strlen($results))-1);
		}else if($result == ""){
			$results = "*";
		}
		if(gettype($obj) == "array"){
			foreach ($obj as $key => $value){
				if(gettype($value) == "string"){
					$value= "'".$value."'";
				}
				$condition .= $key." = ".$value ." and ";
			}
			$condition = substr($condition,0,(strlen($condition)-strlen(" and ")));
			$condition = "where ".$condition;
		}else if($obj == ""){
			$condition= "";
		}
		$sql = "select ".$results." from ".$formName." ".$condition;
		// 		return $sql;
		return $this->activity("c",$sql,0); 
	}
	/**
	 * @select     查询
	 * @$result    需要的字段
	 * @$obj       条件数组
	 * @$sort      排序和分页逻辑  
	 * 				排序   $sort["orderby"] 正序   跟字段名  $sort["orderbydesc"] 倒叙  跟字段名
	 *    			分页$sort["page"]   行数限制  $sort["row"]
	 *    			自定义排序条件   $sort["sql"]
	 *    			模糊搜索   单一条件下的模糊搜索   $obj["like_word"] 字段值    $obj["like_word_value"]  匹配zhi
	 * */	
	public function select($formName,$result,$obj,$sort,$count = false){
		//拼接返回字段
		$results   = "";
		$condition = "";
		$limit = "";
		$sorts = "";
		$likes = "";
		if(gettype($sort) == "array"){ 
			if(isset($sort["orderby"]) != null){
				$sorts = "order by ".$sort["orderby"]."  ";
			} 
			if(isset($sort["orderbydesc"]) != null){
				$sorts = "order by ".$sort["orderbydesc"]." desc ";
			}
			if(isset($sort["sql"]) != null){
				$sorts = $sort["sql"];
			}
			if(isset($sort["page"]) != null){
				$startRow = ($sort["page"]- 1) * $sort["row"];
				$limit = "limit ".$startRow." , ".$sort["row"];
			}
		}
		
		if(gettype($result) == "array"){
			for ($i=0; $i<count($result); $i++)
			{
				$results .= ",".$result[$i];
			}
			$results= substr($results,1,(strlen($results))-1);
		}else if($result == ""){
			$results = "*";
		}
		if(gettype($obj) == "array"&&count($obj)>0){
			if(isset($obj["like_word"])){
				$likes = $obj["like_word"]." like ".$obj["like_word_value"]." and ";
			}
			$word = 0;
			foreach ($obj as $key => $value){
				if($key == "like_word"){
					continue;
				}
				if($key == "like_word_value"){
					continue;
				}
				$word++;
                if(strpos($key,'>') !== false||strpos($key,'<') !== false||strpos($key,'!=') !== false){
                    $condition .= $key.$value ." and ";
                }else if(gettype($value) == "string"){
					$value= "'".$value."'";
					$condition .= $key." = ".$value ." and ";
				}else if(gettype($value) == "integer"){
                    $condition .= $key." = ".$value ." and ";
				}else if(gettype($value) == "array"){
                    for ($x=0; $x<count($value); $x++) {
                        if($x != count($value)-1){
                            if(gettype($value[$x])=="string"){
                                $condition .= $key." = '".$value[$x] ."' || ";
                            }
                            if(gettype($value[$x])=="integer"){
                                $condition .= $key." = ".$value[$x] ." || ";
                            }
                        }else{
                            if(gettype($value[$x])=="string"){
                                $condition .= $key." = '".$value[$x] ."' and ";
                            }
                            if(gettype($value[$x])=="integer"){
                                $condition .= $key." = ".$value[$x] ." and ";
                            }
                        }
                    }
                }
			}
			$condition = substr($condition,0,(strlen($condition)-strlen(" and ")));
			if($likes != ""){
				if($word== 0){
					$likes= substr($likes,0,(strlen($likes)-strlen(" and ")));
				}
				$condition = "where ".$likes."  ".$condition;
			}else{
				$condition = "where ".$condition;
			}
		}else if($obj == ""||count($obj)==0){
			$condition= "";
		}
		if($count){
            $sql = "select ".$results." from ".$formName." ".$condition." ".$sorts;
            $sNum = $this->activity("c",$sql,0);
            return $sNum;
        }
		$sql = "select ".$results." from ".$formName." ".$condition." ".$sorts; 
		$sNum = $this->activity("c",$sql,0); 
		$sql = "select ".$results." from ".$formName." ".$condition." ".$sorts." ".$limit;
		return $this->activity("s",$sql,$sNum);
		// 		$sql = "select * from sc_test";
		// 		$sql = "insert into sc_test (name, type) values ('小明',1)";
		// 		$sql = "update sc_test set name = '小虎dui' where id = 2";
	} 
	/**
	 * @selectJoin   多标联合查询
	 * @$formName    表名数组
	 * @$result      需求字段数组    
	 * @$obj         条件数组	  
	 * 事例：                
	 * $formName[] = "sc_download";
	 * $formName[] = "sc_resources";
	 * $result[] = "sc_resources.title";
	 * $obj["sc_download.res_id"] = "sc_resources.id";
	 * $obj["sc_download.user_id"] = "743085"; 
	 * $sort["page"] = 1;
	 * $sort["row"] = 4;
	 * $sort["orderbydesc"]= "sc_download.time,sc_download.id";
	 * 拼接sql
	 * select sc_resources.title from sc_download,sc_resources where sc_download.res_id = sc_resources.id and sc_download.user_id = 743085 order by sc_download.time,sc_download.id desc limit 0 , 4 
	 * */
	public function selectJoin($formName,$result,$obj,$sort){
		$sorts ="";
		$objs ="";
		$results = "";
		$formNames = "";
		foreach ($result as $key => $value){
			$results.= $value.",";
		}
		foreach ($formName as $key => $value){
			$formNames.= $value.",";
		}
		foreach ($obj as $key => $value){
			$objs .= $key." = ".$value." and ";
		}
		if(gettype($sort) == "array"){
			if(isset($sort["orderby"]) != null){
				$sorts = "order by ".$sort["orderby"]." ";
			}
			if(isset($sort["orderbydesc"]) != null){
				$sorts = "order by ".$sort["orderbydesc"]." desc ";
			}
			if(isset($sort["sql"]) != null){
				$sorts = $sort["sql"];
			}
			if(isset($sort["page"]) != null){
				$startRow = ($sort["page"]- 1) * $sort["row"];
				$limit = "limit ".$startRow." , ".$sort["row"];
			}
		}
		$formNames= substr($formNames,0,(strlen($formNames))-1);
		$results = substr($results,0,(strlen($results))-1);
		$objs= substr($objs,0,strlen($objs)-(strlen(" and ")));
		$sql = "select ".$results." from ".$formNames." where ".$objs." ".$sorts;
		$sNum = $this->activity("c",$sql,0);
		$sql = "select ".$results." from ".$formNames." where ".$objs." ".$sorts." ".$limit;
		return $this->activity("s",$sql,$sNum);
	} 
	
	/**
	 * @update      修改数据
	 * @$formName   表名
	 * @$changes    修改数据数组    #支持计数器操作，直接传入字符串  如：coll_num = coll_num+1       
	 * @$condition  条件数组
	 * */
	public function update($formName,$change,$condition){
// 		$sql = "update sc_test set name = '小虎dui' where id = 2";
		$changes = "";
		$conditions = "";
		if(gettype($change) == "array"){
			foreach ($change as $key => $value){
				if(gettype($value) == "string"){
					$value= "'".$value."'";
				}
				$changes .= $key." = ".$value .",";
			}
			$changes= substr($changes,0,(strlen($changes) - 1));
		}
		if(gettype($change) == "string"){
			$changes = $change;
		}
		foreach ($condition as $key => $value){
			if(gettype($value) == "string"){
				$value= "'".$value."'";
			}
			$conditions.= $key." = ".$value ." and ";
		}
		$conditions = substr($conditions,0,(strlen($conditions) - strlen(" and ")));
		$conditions = " where ".$conditions;
		$sql ="update ".$formName." set ".$changes." ".$conditions;
		return $this->activity("u",$sql,0); 
	}
	/**
	 * @delete      删除语句
	 * @$formName   表名
	 * @$condition  删除条件数组
	 * */
	public function delete($formName,$condition){
// 		$sql = "delete from sc_test where id = 2";
		$conditions = "";
		foreach ($condition as $key => $value){
			if(gettype($value) == "string"){
				$value= "'".$value."'";
			}
			$conditions .= $key." = ".$value ." and ";
		}
		$conditions = substr($conditions,0,(strlen($conditions) - strlen(" and ")));
		$sql = "delete from ".$formName." where ".$conditions;
		return $this->activity("d",$sql,0); 
	}

}
?>