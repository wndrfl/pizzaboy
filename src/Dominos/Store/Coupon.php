<?php
namespace Dominos\Store;

class Coupon
{
	private
		$_code,
		$_description,
		$_groupCodes,
		$_imageCode,
		$_largeSizeImageUrl,
		$_name,
		$_price,
		$_tags,
		$_thumbNailSizeImageUrl,
		$_sortSeq;
	
	public function code()
	{
		return $this->_code;
	}
	
	public function description()
	{
		return $this->_description;
	}
	
	public function groupCodes()
	{
		return $this->_groupCodes;
	}
	
	public function imageCode()
	{
		return $this->_imageCode;
	}
	
	public function largeSizeImageUrl()
	{
		return $this->_largeSizeImageUrl;
	}
	
	public function name()
	{
		return $this->_name;
	}
	
	public function prize()
	{
		return $this->_price;
	}
	
	public function setCode($code)
	{
		$this->_code = $code;
		return $this;
	}
	
	public function setDescription($description)
	{
		$this->_description = $description;
		return $this;
	}
	
	public function setGroupCodes($groupCodes)
	{
		$this->_groupCodes = $groupCodes;
		return $this;
	}
	
	public function setImageCode($imageCode)
	{
		$this->_imageCode = $imageCode;
		return $this;
	}
	
	public function setLargeSizeImageUrl($largeSizeImageUrl)
	{
		$this->_largeSizeImageUrl = $largeSizeImageUrl;
		return $this;
	}
	
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	
	public function setPrice($price)
	{
		$this->_price = $price;
		return $this;
	}
	
	public function setSortSeq($sortSeq)
	{
		$this->_sortSeq = $sortSeq;
		return $this;
	}
	
	public function setTags($tags)
	{
		$this->_tags = $tags;
		return $this;
	}
	
	public function setThumbNailSizeImageUrl($thumbNailSizeImageUrl)
	{
		$this->_thumbNailSizeImageUrl = $thumbNailSizeImageUrl;
		return $this;
	}
	
	public function sortSeq()
	{
		return $this->_sortSeq;
	}
	
	public function tags()
	{
		return $this->_tags;
	}
	
	public function thumbNailSizeImageUrl()
	{
		return $this->_thumbNailSizeImageUrl;
	}
}