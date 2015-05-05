# easyorm
Codeigniter ORM - easy, easy, easy



## 소개

JAVA jpa에 영감을 받아 만든 코드이그나이터 모델을 확장하여 CRUD를 제공하는 확장 클래스 입니다.


## 특징

Codeigniter 기반
모델과 컬럼을 1:1로 매칭함
property가 배열일경우 json으로 치환하여 저장함
json으로 저장된 데이터의 경우 배열로 치환하여 호출함

## 사용법

* MY_Model.php 를 다운받아 core 폴더에 저장함
* 사용자 모델을 CI_Model에서 MY_Model로 확장함

Model
```
  class Usermodel extends MY_Model{
    
    var $id = 0;
    var $screenname = '';
    var $password = '';
    var $hobbies = array();
    var $regDate = '';
    var $lastDate = '';
    var $regIp = '';
    
    
    // _(언더스코어)로 시작되는 컬럼은 ORM에서 무시함
    var $_key = 'id';
    var $_table = 'user';

    // 데이터가 저장되기전 처리
    public function prepersist(){
      $this->regDate = 'NOW()';
      $this->regIp = $this->input->ip_addresss();
    }
    
    // 데이터가 업데이트 되기 전 처리
    public function preupdate(){
      $this->lastDate = 'NOW()';
    }

    // 데이터가 삭제되기 전 처리
    public function predelete(){
      log('삭제됨 :' . date('y-m-d h:i:s')'
    }

  }

```


Controller
```
  class Controll extends CI_Controller{

    public function __construct(){
      $this->load->model('usermodel');
    }

    public function inAction(){

      $this->load->model('usermodel');
      
      // 모델에 포스트로 넘어온값 바인딩
      $this->usermodel->initialize($this->input->post(), array('screenname', 'password'));
      
      // 저장하기
      $this->usermodel->save();

      redirect('/user/'.$this->usermodel->id);

    }
  
    public function view($id){

      // 키값으로 데이터 조회
      // 조회후 값이 $this->usermodel에 바인딩 됨
      $this->usermodel->get($id);    
    
      $this->load->view('view',array('user' => $this->usermodel);
    
    }
  
    public function inAction(){

      // 모델에 포스트로 넘어온값 바인딩
      // 2번째 파라미터는 바인딩을 제외하는 컬럼을 지정 (id, 아이피등)
      $this->usermodel->initialize($this->input->post(), array('id'));

      // 저장하기
      // 저장할때 prepersist() 함수를 체크하여 사전 처리 진행
      $this->usermodel->save();

      redirect('/user/'.$this->usermodel->id);

    }
    
    public function upAction(){

      $this->usermodel->initialize($this->input->post(), array('id'));

      // 업데이트
      // 업데이트 preupdate() 함수를 체크하여 사전 처리 진행
      // 2번째 파라미터는 업데이트를 할 컬럼을 지정 (지정안하면 전체 컬럼 업데이트)
      $this->usermodel->update(array('password'));

    }

    public function deleteAction($id){
    
      // 데이터 호출하기
      $this->usermodel->get($id);
      
      // 객체를 삭제하기
      $this->usermodel->delete();

      redirect('user');
    }

  }
```
