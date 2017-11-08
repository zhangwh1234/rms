<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/23
 * Time: 下午5:18
 * 堂口退单
 */

class DiningReturnAction extends ModuleAction
{


    // 返回一些其他的数据,比如下拉列表框等的数据
    public function returnMainFnPara()
    {
        // 分公司的数据
        $companymgr_model = D('companymgr');
        $where = array();
       // $where['telphoneauto'] = '营业';
       // $where['domain'] = $this->getDomain();
        $companymgr = $companymgr_model->field('name')->where($where)->select();
        // 在company字段中写入值
        $this->assign('companymgr', $companymgr);

    }

    /**
     * 根据序号,返回分公司的堂口销售金额
     * @param sequence
     * @param company
     */
    public  function getMoney(){
        //序号
        $sequence = $_REQUEST['sequence'];
        //分公司
        $company = $_REQUEST['company'];

        $diningsaleModel = D('diningsale');
        $where = array();
        $where['sequence'] = $sequence;
        $where['company'] = $company;

        $diningsaleResult = $diningsaleModel->where($where)->find();
        if($diningsaleResult){
            $money = $diningsaleResult['money'];
        }else{
            $money = 0;
        }

        $info = array();
        $info['money'] = $money;
        $this->ajaxReturn($info);
    }

    /**
     * 保存数据的补充
     *
     */
    public function autoParaInsert()
    {
        return array(
            array(
                'time',
                date('Y-m-d H:i:s')
            ),
            array(
                'domain',
                $this->getDomain()
            )
        );
    }

    /**
     * 保存附件
     */
    public function save_slave_table($record){

        $company = $_REQUEST['company'];
        $ap   = $this->getAp();
        $money = $_REQUEST['money'];

        //将金额保存到diningcollect表中
        $where = array();
        $where ['domain'] = $this->getDomain();
        $where ['company'] = $company;
        $where ['date'] = date('H-m-d');
        $where ['ap'] = $ap;

        $diningcollectModel = D('diningcollect');
        $diningcollectResult = $diningcollectModel->where($where)->find();
        if(empty($diningcollectResult)){
            $data = array();
            $data['domain'] = $this->getDomain();
            $data['company'] = $company;
            $data['date'] = date('H-m-d');
            $data['ap'] = $ap;
            $data['money'] = -$money;
            $diningcollectModel->add($data);
        }else{
            $where = array();
            $where['diningcollectid'] = $diningcollectResult['diningcollectid'];
            $data = array();
            $data ['money'] = $diningcollectResult['money'] - $money;
            $diningcollectModel->where($where)->save($data);
        }

    }

    
}