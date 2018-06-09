# logic
phplogic
1.2018.06.09 首次提交，本phplogic项目作为小型项目中数据库mysql框架的连接模板，可以快速进行小微型项目数据库操作，可以快速搭建请求模块，返回接口数据，返回json格式数据。
优势：
    1.仅有一个文件，作为轻量级数据库操作模块，可以应用在每一个php项目中，无任何框架，无需繁复引用文件
    2.快速搭建，用最简单的语言实现最复杂的功能，在搭建项目时只考虑逻辑，不需要担心数据类型和繁琐的sql处理
    3.不需要写sql语句，真正的做到了只考虑逻辑，节省大量代码
    4.返回的数据全面而精准，包括状态，sql执行遇到的错误，返回sql语句，方便直接联调数据库测试
返回结构：
          
          {
              "code":0,
              "sql":"select id,title,explains,cover,time from sc_news order by time desc limit 0 , 4",
              "list":[
                  {
                      "id":"97",
                      "title":"商标转让费用多少钱？商标转让需要哪些流程？",
                      "explains":"商标转让费用多少钱？",
                      "cover":"http://121.43.37.189/tmAdmin/uploads/newsCover/20180516/182806138.jpg",
                      "time":"2018-05-16 18:28:06"
                  }
              ],
              "allCount":30,
              "count":4,
              "msg":"success"
          }
          
          code  0   查询有值、执行成功，删除成功，修改成功等操作
                2   数据库字段匹配错误，sql语句错误
                3   没查询到值
          sql       执行的sql
          list      返回的结果集合
          allCount  同等条件下所有的数据数量，便于分页，显示总数等使用
          count     本页有多少条数据
          msg       返回消息
    使用方法： 
        4.1 引用
          include 'db/logic.php';
        4.2 实例化：
          $fun = new logic();
        4.3操作：
          插入      insert("表名",“插入集合”)
          批量插入  inserts("表名",“数据集合”)
          查询      select(“表名【字符串】”，“返回字段【数组】”，“条件集【数组、字符串】”，“分页操作，模糊查询等”，“是否仅仅返回数量，可以传布尔值，不传值默认返回所有”);
          修改      update("表名","修改键值对数组","条件集合");
          删除      delete("表名",“条件集”);
          4.3.0 返回字段值（传入字段值array即可）
          echo json_encode($fun->select("tr_company",array('id','name','s_option'),$obj,""));
          4.3.1 插入语句（key=字段名  value=匹配值）
          $obj = array();
          $obj["uid"] = $_POST["uid"];
          $obj["name"] = $_POST["name"];
          $obj["input_time"] = date("Y-m-d H:i:s",time());
          $obj["state"] = 0;
          //得到执行结果输出json
          echo json_encode($fun->insert("tr_purchase",$obj));
          4.3.2 查询
            $obj = array();
            $obj["template"] = $_POST['template'];
            $obj["name"] = $_POST['name'];
            $company = $fun->select("tr_company","",$obj,"");
            echo json_encode(company);
            //分页操作  page  第几行     row   列数
             $sort["page"] = $_POST["page"];
             $sort["row"] = 8; 
             //排序操作  orderbydesc   降序             orderby   升序
             $sort["orderbydesc"] = "upload_time";
             $company = $fun->select("tr_company","",$obj,$sort);
             echo json_encode(company);
             //模糊查询
             $obj["like_word"] = "字段名";
             $obj["like_word_value"] = "'%".$_POST["模糊值"]."%'";
             $fun->select("tr_company","",$obj,"");
             //多值查询，多表联合查询(查询某字段对应的多个值)【多表联合查询时可分步骤使用此方法，效率提升n倍】
             $id[] = 1;
             $id[] = 2;
             $id[] = 3;
             $id[] = 4;
             $obj["id"] = $id;
             $fun->select("tr_company","",$obj,"");
             //对比符条件查询 【可以是  >   <   != 】
              $obj = array();
              $obj["user_id"] =  $_POST["uid"];
              $obj["s_option !="] =  2;
              echo json_encode($fun->select("tr_company",array( 'name','s_option'),$obj,""));
           4.3.3 修改操作
              $obj["name"] = $_POST["name"];
              $c["id"] = $_POST["historyId"];
              echo json_encode($fun->update("tr_service",$obj,$c));
              //计数器操作（用于增加访问量，浏览量等快速修改）
              $obj["id"] = $_POST["h_id"];
              $change = "page_view = page_view + 1 ";
              $res = $db->update($oe_content,$change,$obj);
           4.3.4  删除操作
              $objs["id"] = $_POST["c_id"]; 
              $ren = $db->delete($oe_collect,$obj);

