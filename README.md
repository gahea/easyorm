# easyorm
Codeigniter ORM - easy, easy, easy



## 소개

코드이그나이터 모델을 확장하여 CRUD를 제공하는 확장 클래스 입니다.



## 사용법

Model
```
  class Usermodel extends CI_Model{
    
    var $id = 0;
    var $screenname = '';
    var $password = '';
    var $regDate = '';
    var $lastDate = '';
    var $regIp = '';
    
    var $_key = 'id';
    var $_table = 'user';

    // 데이터가 저장되기전 처리내역
    public function prepersist(){
      $this->regDate = 'NOW()';
      $this->regIp = $this->input->ip_addresss();
    }
    
    public function preupdate(){
      $this->lastDate = 'NOW()';
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
