<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package content
 * @subpackage forms
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Content_Form_Menuitems extends menu_Form_Item
{

    /**
     * (non-PHPdoc)
     * @see menu_Form_Item::init()
     */
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        parent::init();
        switch ($this->getAttrib('menuType')) {
        	case 'articles-list-registered':
        		break;
        	case 'article-add':
        	    break;
        	case 'category-list-registered':
        	    break;
        	case 'category-add':
        	    break;
        	case 'article-simple-layout':
        	    $this->addSimpleLayout();
        	    break;
        	case 'category-blog-layout':
        	    $this->addCategoryBlogLayout();
        	    break;
        	default:
        		$this->defaultParent();
        		break;
        }
    }
    
    /**
     * 
     */
    private function defaultParent()
    {
    }
    
    /**
     * Add custom fields to menu item form for render a simple layout content form menu item 
     */
    function addSimpleLayout()
    {
        $this->setAttrib('enctype', 'multipart/form-data');
        
        #$this->defaultFormFields[] = 'articleFilter';
        
        /*$hArticleId = $this->createElement("hidden", "aid")
        	->setDecorators( array('ViewHelper') );
        $this->addElement( $hArticleId );
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $txtFilter = new ZendX_JQuery_Form_Element_AutoComplete("articleFilter");
        $txtFilter->setLabel( 'CONTENT_FILTER_BY_ARTICLE' );
	    $txtFilter->setAttrib('size',40);
	    $txtFilter->setJQueryParam('source', $view->baseUrl() ."/content/article/getbychar/");
	    #$txtFilter->setJQueryParam('source', "content/article/getbychar/");
        $this->addElement($txtFilter);*/
        
        /* @var $cbArticle Zend_Form_Element_Select */
        $cbArticle = $this->createElement('select', 'aid');
        $cbArticle->setLabel("CONTENT_COD_ARTICLE");
        $cbArticle->setRequired(true);
        $cbArticle->setOrder( $this->order++ );
        $mdlArticle = new Content_Model_Article();
        $articles = $mdlArticle->getList();
        foreach ( $articles as $article ) {
            $cbArticle->addMultiOption($article->id, $article->title);
        }
        $this->addElement($cbArticle);
        
    }
    
    /**
     * Add custom fields to menu item form for render a categpru blog layout form menu item
     */
    function addCategoryBlogLayout()
    {
        $cbCategory = $this->createElement('select', 'catid')
	        ->setLabel( "LBL_CATEGORY" )
	        ->setOrder( $this->order++ )
	        ->setRequired(true);
        $mdlCategory = new Content_Model_Category();
        $categoryList = $mdlCategory->getSimpleList();
        foreach ( $categoryList as $category )
        {
            $cbCategory->addMultiOption( $category->id, $category->title );
        }
        $this->addElement($cbCategory);
        
        $cbOrderField = $this->createElement('select', 'of')
	        ->setLabel( "CONTENT_ORDER_FIELD" )
	        ->setOrder( $this->order++ )
	        ->setRequired(true);
        # we can add more type like publish date, author name....
        # by default ordering and id only
        $cbOrderField->addMultiOption( '1', 'LBL_ORDERING' );
        $cbOrderField->addMultiOption( '2', 'LBL_ID' );
        $this->addElement($cbOrderField);
        
        $cbOrderType = $this->createElement('select', 'ot')
	        ->setLabel( "CONTENT_ORDER_TYPE" )
	        ->setOrder( $this->order++ )
	        ->setRequired(true);
        $cbOrderType->addMultiOption( 'asc', 'asc' );
        $cbOrderType->addMultiOption( 'desc','desc' );
        $this->addElement($cbOrderType);
    }

}

