<?php

class Acl_Model_Accountdetail extends Zend_Db_Table_Abstract
{

    protected $_name = 'cntbl_account_detail';
    
    protected $_referenceMap = array (
        'refAccount' => array(
            'columns'		=> array ( 'in_id_account' ),
            'refTableClass'	=> 'Acl_Model_Account',
            'refColumns'	=> array ( 'id' ),
        ),
        'refSector' => array(
            'columns'		=> array ( 'in_id_sector' ),
            'refTableClass'	=> 'Geo_Model_Sector',
            'refColumns'	=> array ( 'in_id' ),
        ),
    );
    
    /**
     * retorna el id de distrito, provincia y region que abarcan los sectores de un usuario
     * @param bigint $usuarioId
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    function getGeoIdsParents($usuarioId)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from( array('ad'=>$this->_name), array() );
        $select->joinInner( array('sec'=>'cntbl_geo_sector') , 'sec.in_id=ad.in_id_sector');
        $select->joinInner( array('loc'=>'cntbl_geo_localidad') , 'loc.in_id=sec.in_id_localidad');
        $select->joinInner( array('dst'=>'cntbl_geo_distrito') , 'dst.in_id=loc.in_id_distrito', array('in_id AS id_distrito'));
        $select->joinInner( array('prv'=>'cntbl_geo_provincia') , 'prv.in_id=dst.in_id_provincia', array('in_id AS id_provincia'));
        $select->joinInner( array('reg'=>'cntbl_geo_region') , 'reg.in_id=prv.in_id_region', array('in_id AS id_region', 'vc_nombre AS nombre_region'));
        $select->where('ad.in_id_account=?', $usuarioId, Zend_Db::BIGINT_TYPE);
        $select->where('reg.bol_activo=1');
        $select->limit(1);
        return $this->fetchRow($select);
    }
    
}

