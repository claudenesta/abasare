<?php
require_once "DBController.php";

class ShoppingCart extends DBController
{

    function getAllProduct()
    {
        $query = "SELECT * FROM products ORDER BY `id` DESC ";
        
        $productResult = $this->getDBResult($query);
        return $productResult;
    }
    
    function UpdateProduct($aaa)
    {
        $query = "SELECT * FROM products WHERE id=$aaa ORDER BY `id` DESC ";
        
        $productResult = $this->getDBResult($query);
        return $productResult;
    }
    
     function getAllSuppliers()
    {
        $query = "SELECT * FROM suppliers";
        
        $supplierResult = $this->getDBResult($query);
        return $supplierResult;
    }

 function getAlluser()
    {
        $query = "SELECT * FROM `users`";
        
        $userresult = $this->getDBResult($query);
        return $userresult;
    }
    function getUserUpdate()
    {
        $query = "SELECT * FROM `users` WHERE id=$id";
        
        $userresult = $this->getDBResult($query);
        return $userresult;
    }
    function getMemberCartItem($member_id)
    {
        $query = "SELECT products.*,products.id as uni, Purchase_cart.id as cart_id,Purchase_cart.quantity FROM products, Purchase_cart WHERE 
            products.id = Purchase_cart.product_id AND Purchase_cart.member_id = ?";
        
        $params = array(
            array(
                "param_type" => "i",
                "param_value" => $member_id
            )
        );
        
        $cartResult = $this->getDBResult($query, $params);
        return $cartResult;
    }

    function getProductByCode($product_code)
    {
        $query = "SELECT * FROM products WHERE code=?";
        
        $params = array(
            array(
                "param_type" => "s",
                "param_value" => $product_code
            )
        );
        
        $productResult = $this->getDBResult($query, $params);
        return $productResult;
    }

    function getCartItemByProduct($product_id, $member_id)
    {
        $query = "SELECT * FROM Purchase_cart WHERE product_id = ? AND member_id = ?";
        
        $params = array(
            array(
                "param_type" => "i",
                "param_value" => $product_id
            ),
            array(
                "param_type" => "i",
                "param_value" => $member_id
            )
        );
        
        $cartResult = $this->getDBResult($query, $params);
        return $cartResult;
    }

    function getMaxCart()
    {
        $query = "SELECT max(id) as iddf FROM `Purchase_cart`";
        $maxcartResult = $this->getDBResult($query, $params);
        return $maxcartResult;
    }

    
    function addToCart($product_id, $quantity, $member_id, $invoice)
    {
	
        $query = "INSERT INTO Purchase_cart (product_id,quantity,member_id,invoice) VALUES (?, ?, ?, ?)";
        
        $params = array(
            array(
                "param_type" => "i",
                "param_value" => $product_id
            ),
            array(
                "param_type" => "i",
                "param_value" => $quantity
            ),
            array(
                "param_type" => "i",
                "param_value" => $member_id
            ),
			  array(
                "param_type" => "i",
                "param_value" => $invoice
            )
        );
		
$this->updateDB($query, $params);

	header("location:sales.php?transaction=$invoice");
    }
    function updateCartQuantity($quantity, $cart_id, $invoice)
    {
        $query = "UPDATE Purchase_cart SET  quantity = ? WHERE id= ?";
                
        $params = array(
            array(
                "param_type" => "i",
                "param_value" => $quantity
            ),
            array(
                "param_type" => "i",
                "param_value" => $cart_id
            )
        );
        
        $this->updateDB($query, $params);
		
		$query1 = "UPDATE sales_order SET  quantity = ? WHERE cart_id= ?";
                
        $params1 = array(
            array(
                "param_type" => "i",
                "param_value" => $quantity
            ),
            array(
                "param_type" => "i",
                "param_value" => $cart_id
            )
        );
		
		$this->updateDB($query1, $params1);
	   
	   header("location:sales.php?transaction=$invoice");
	   }

    function deleteCartItem($cart_id)
    {
        $query = "DELETE FROM Purchase_cart WHERE id = ?";
        
        $params = array(
            array(
                "param_type" => "i",
                "param_value" => $cart_id
            )
        );
        
        $this->updateDB($query, $params);
    }

    function emptyCart($member_id)
    {
        $query = "DELETE FROM Purchase_cart WHERE member_id = ?";
        
        $params = array(
            array(
                "param_type" => "i",
                "param_value" => $member_id
            )
        );
        
        $this->updateDB($query, $params);
    }
}
