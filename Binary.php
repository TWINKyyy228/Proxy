<?php

class Binary{
	public static function signByte(int $value) : int{
		return $value << 56 >> 56;
	}

	public static function unsignByte(int $value) : int{
		return $value & 0xff;
	}

	public static function signShort(int $value) : int{
		return $value << 48 >> 48;
	}

	public static function unsignShort(int $value) : int{
		return $value & 0xffff;
	}

	public static function signInt(int $value) : int{
		return $value << 32 >> 32;
	}

	public static function unsignInt(int $value) : int{
		return $value & 0xffffffff;
	}

	public static function flipShortEndianness(int $value) : int{
		return self::readLShort(self::writeShort($value));
	}

	public static function flipIntEndianness(int $value) : int{
		return self::readLInt(self::writeInt($value));
	}

	public static function flipLongEndianness(int $value) : int{
		return self::readLLong(self::writeLong($value));
	}

	/**
	 * @return mixed[]
	 */
	private static function safeUnpack(string $formatCode, string $bytes) : array{
		//unpack SUCKS SO BADLY. We really need an extension to replace this garbage :(
		$result = unpack($formatCode, $bytes);
		if($result === false){
			//assume the formatting code is valid, since we provided it
			throw new InvalidArgumentException("Invalid input data (not enough?)");
		}
		return $result;
	}

	/**
	 * Reads a byte boolean
	 *
	 * @param string $b
	 *
	 * @return bool
	 */
	public static function readBool(string $b) : bool{
		return $b !== "\x00";
	}

	/**
	 * Writes a byte boolean
	 *
	 * @param bool $b
	 *
	 * @return string
	 */
	public static function writeBool(bool $b) : string{
		return $b ? "\x01" : "\x00";
	}

	/**
	 * Reads an unsigned byte (0 - 255)
	 *
	 * @param string $c
	 *
	 * @return int
	 */
	public static function readByte(string $c){
		return ord($c[0]);
	}

	/**
	 * Reads a signed byte (-128 - 127)
	 * 
	 * @param string $c
	 *
	 * @return int
	 */
	public static function readSignedByte(string $c) : int{
		return self::signByte(ord($c[0]));
	}

	/**
	 * Writes an unsigned/signed byte
	 *
	 * @param int $c
	 *
	 * @return string
	 */
	public static function writeByte(int $c) : string{
		return chr($c);
	}

	/**
	 * Reads a 16-bit unsigned big-endian number
	 *
	 * @param string $str
	 *
	 * @return int
	 */
	public static function readShort($str){
		return self::safeUnpack("n", $str)[1];
	}

	/**
	 * Reads a 16-bit signed big-endian number
	 *
	 * @param string $str
	 *
	 * @return int
	 */
	public static function readSignedShort($str){
        return self::signShort(self::safeUnpack("n", $str)[1]);
	}

	/**
	 * Writes a 16-bit signed/unsigned big-endian number
	 *
	 * @param int $value
	 *
	 * @return string
	 */
	public static function writeShort($value){
		return pack("n", $value);
	}

	/**
	 * Reads a 16-bit unsigned little-endian number
	 *
	 * @param string $str
	 *
	 * @return int
	 */
	public static function readLShort($str){
		return self::safeUnpack("v", $str)[1];
	}

	/**
	 * Reads a 16-bit signed little-endian number
	 *
	 * @param string $str
	 *
	 * @return int
	 */
	public static function readSignedLShort($str){
		return self::signShort(self::safeUnpack("v", $str)[1]);
	}

	/**
	 * Writes a 16-bit signed/unsigned little-endian number
	 *
	 * @param int $value
	 *
	 * @return string
	 */
	public static function writeLShort($value){
		return pack("v", $value);
	}

	/**
	 * Reads a 3-byte big-endian number
	 *
	 * @param string $str
	 * 
	 * @return int
	 */
	public static function readTriad(string $str) : int{
		return self::safeUnpack("N", "\x00" . $str)[1];
	}

	/**
	 * Writes a 3-byte big-endian number
	 *
	 * @param int $value
	 * 
	 * @return string
	 */
	public static function writeTriad(int $value) : string{
		return substr(pack("N", $value), 1);
	}

	/**
	 * Reads a 3-byte little-endian number
	 *
	 * @param string $str
	 * 
	 * @return int
	 */
	public static function readLTriad(string $str) : int{
		return self::safeUnpack("V", $str . "\x00")[1];
	}

	/**
	 * Writes a 3-byte little-endian number
	 *
	 * @param int $value
	 * 
	 * @return string
	 */
	public static function writeLTriad(int $value) : string{
		return substr(pack("V", $value), 0, -1);
	}

	/**
	 * Reads a 4-byte signed integer
	 *
	 * @param string $str
	 * 
	 * @return int
	 */
	public static function readInt(string $str) : int{
		return self::signInt(self::safeUnpack("N", $str)[1]);
	}

	/**
	 * Writes a 4-byte integer
	 *
	 * @param int $value
	 * 
	 * @return string
	 */
	public static function writeInt(int $value) : string{
		return pack("N", $value);
	}

	/**
	 * Reads a 4-byte signed little-endian integer
	 *
	 * @param string $str
	 * 
	 * @return int
	 */
	public static function readLInt($str){
		return self::signInt(self::safeUnpack("V", $str)[1]);
	}

	/**
	 * Writes a 4-byte signed little-endian integer
	 *
	 * @param int $value
	 * 
	 * @return string
	 */
	public static function writeLInt($value){
		return pack("V", $value);
	}

	/**
	 * Reads a 4-byte floating-point number
	 *
	 * @param string $str
	 * 
	 * @return float
	 */
	public static function readFloat($str){
		return self::safeUnpack("G", $str)[1];
	}

	/**
	 * Reads a 4-byte floating-point number, rounded to the specified number of decimal places.
	 *
	 * @param string $str
	 * @param int $accuracy
	 *
	 * @return float
	 */
	public static function readRoundedFloat(string $str, int $accuracy) : float{
		return round(self::readFloat($str), $accuracy);
	}

	/**
	 * Writes a 4-byte floating-point number.
	 *
	 * @param float $value
	 * 
	 * @return string
	 */
	public static function writeFloat($value){
		return pack("G", $value);
	}

	/**
	 * Reads a 4-byte little-endian floating-point number.
	 *
	 * @param string $str
	 * 
	 * @return float
	 */
	public static function readLFloat($str){
		return self::safeUnpack("g", $str)[1];
	}

	/**
	 * Reads a 4-byte little-endian floating-point number rounded to the specified number of decimal places.
	 *
	 * @param string $str
	 * @param int $accuracy
	 *
	 * @return float
	 */
	public static function readRoundedLFloat(string $str, int $accuracy) : float{
		return round(self::readLFloat($str), $accuracy);
	}

	/**
	 * Writes a 4-byte little-endian floating-point number.
	 *
	 * @param float $value
	 * 
	 * @return string
	 */
	public static function writeLFloat($value){
		return pack("g", $value);
	}

	/**
	 * Returns a printable floating-point number.
	 *
	 * @param float $value
	 * 
	 * @return string
	 */
	public static function printFloat($value) : string{
		return preg_replace("/(\\.\\d+?)0+$/", "$1", sprintf("%F", $value));
	}

	/**
	 * Reads an 8-byte floating-point number.
	 *
	 * @param string $str
	 * 
	 * @return float
	 */
	public static function readDouble($str){
		return self::safeUnpack("E", $str)[1];
	}

	/**
	 * Writes an 8-byte floating-point number.
	 *
	 * @param float $value
	 * 
	 * @return string
	 */
	public static function writeDouble($value){
		return pack("E", $value);
	}

	/**
	 * Reads an 8-byte little-endian floating-point number.
	 *
	 * @param string $str
	 * 
	 * @return float
	 */
	public static function readLDouble($str){
		return self::safeUnpack("e", $str)[1];
	}

	/**
	 * Writes an 8-byte floating-point little-endian number.
	 * 
	 * @param float $value
	 * 
	 * @return string
	 */
	public static function writeLDouble($value){
		return pack("e", $value);
	}

	/**
	 * Reads an 8-byte integer.
	 *
	 * @param string $str
	 * 
	 * @return int
	 */
	public static function readLong($str){
		return self::safeUnpack("J", $str)[1];
	}

	/**
	 * Writes an 8-byte integer.
	 *
	 * @param int $value
	 * 
	 * @return string
	 */
	public static function writeLong($value){
		return pack("J", $value);
	}

	/**
	 * Reads an 8-byte little-endian integer.
	 *
	 * @param string $str
	 * 
	 * @return int
	 */
	public static function readLLong($str){
		return self::safeUnpack("P", $str)[1];
	}

	/**
	 * Writes an 8-byte little-endian integer.
	 *
	 * @param int $value
	 * 
	 * @return string
	 */
	public static function writeLLong($value){
		return pack("P", $value);
	}

	/**
	 * @param $stream
	 *
	 * @return int
	 */
	public static function readVarInt(string $buffer, int &$offset) : int{
		$raw = self::readUnsignedVarInt($buffer, $offset);
		$temp = ((($raw << 63) >> 63) ^ $raw) >> 1;
		return $temp ^ ($raw & (1 << 63));
	}

	/**
	 * @param $stream
	 *
	 * @return int
	 */
	public static function readUnsignedVarInt(string $buffer, int &$offset) : int{
		$value = 0;
		for($i = 0; $i <= 28; $i += 7){
			if(!isset($buffer[$offset])){
				throw new InvalidArgumentException("No bytes left in buffer");
			}
			$b = ord($buffer[$offset++]);
			$value |= (($b & 0x7f) << $i);

			if(($b & 0x80) === 0){
				return $value;
			}
		}

		throw new InvalidArgumentException("VarInt did not terminate after 5 bytes!");
	}

	/**
	 * @param int $v
	 * 
	 * @return string
	 */
	public static function writeVarInt($v){
		$v = ($v << 32 >> 32);
		return self::writeUnsignedVarInt(($v << 1) ^ ($v >> 31));
	}

	/**
	 * @param int $value
	 *
	 * @return string
	 */
	public static function writeUnsignedVarInt($value){
		$buf = "";
		$value &= 0xffffffff;
		for($i = 0; $i < 5; ++$i){
			if(($value >> 7) !== 0){
				$buf .= chr($value | 0x80);
			}else{
				$buf .= chr($value & 0x7f);

				return $buf;
			}
			$value = (($value >> 7) & (PHP_INT_MAX >> 6)); //PHP really needs a logical right-shift operator
		}
		throw new InvalidArgumentException("Value too large to be encoded as a VarInt");
	}

	/**
	 * Reads a 64-bit zigzag-encoded variable-length integer.
	 * 
	 * @param string $buffer
	 * @param int    &$offset
	 * 
	 * @return int
	 */
	public static function readVarLong(string $buffer, int &$offset){
		$raw = self::readUnsignedVarLong($buffer, $offset);
		$temp = ((($raw << 63) >> 63) ^ $raw) >> 1;
		return $temp ^ ($raw & (1 << 63));
	}

	/**
	 * Reads a 64-bit unsigned variable-length integer.
	 *
	 * @param string $buffer
	 * @param int    &$offset
	 *
	 * @return int
	 */
	public static function readUnsignedVarLong(string $buffer, int &$offset){
		$value = 0;
		for($i = 0; $i <= 63; $i += 7){
			if(!isset($buffer[$offset])){
				throw new InvalidArgumentException("No bytes left in buffer");
			}
			$b = ord($buffer[$offset++]);
			$value |= (($b & 0x7f) << $i);

			if(($b & 0x80) === 0){
				return $value;
			}
		}

		throw new InvalidArgumentException("VarLong did not terminate after 10 bytes!");
	}

	/**
	 * Writes a 64-bit integer as a zigzag-encoded variable-length long.
	 * 
	 * @param int $v
	 *
	 * @return string
	 */
	public static function writeVarLong($v){
		return self::writeUnsignedVarLong(($v << 1) ^ ($v >> 63));
	}

	/**
	 * Writes a 64-bit unsigned integer as a variable-length long.
	 * 
	 * @param int $value
	 *
	 * @return string
	 */
	public static function writeUnsignedVarLong($value) : string{
		$buf = "";
		for($i = 0; $i < 10; ++$i){
			if(($value >> 7) !== 0){
				$buf .= chr($value | 0x80); //Let chr() take the last byte of this, it's faster than adding another & 0x7f.
			}else{
				$buf .= chr($value & 0x7f);

				return $buf;
			}
			$value = (($value >> 7) & (PHP_INT_MAX >> 6)); //PHP really needs a logical right-shift operator
		}
		throw new InvalidArgumentException("Value too large to be encoded as a VarLong");
	}
}

class BinaryStream{

	/** @var int */
	public $offset;
	/** @var string */
	public $buffer;

	public function __construct(string $buffer = "", int $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}

	public function reset(){
		$this->buffer = "";
		$this->offset = 0;
	}

	/**
	 * Rewinds the stream pointer to the start.
	 */
	public function rewind() : void{
		$this->offset = 0;
	}

	public function setOffset(int $offset) : void{
		$this->offset = $offset;
	}

	public function setBuffer(string $buffer = "", int $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}

	public function getOffset() : int{
		return $this->offset;
	}

	public function getBuffer() : string{
		return $this->buffer;
	}

	/**
	 * @param int|true $len
	 *
	 * @throws InvalidArgumentException if there are not enough bytes left in the buffer
	 */
	public function get($len) : string{
		if($len === 0){
			return "";
		}

		$buflen = strlen($this->buffer);
		if($len === true){
			$str = substr($this->buffer, $this->offset);
			$this->offset = $buflen;
			return $str;
		}
		if($len < 0){
			$this->offset = $buflen - 1;
			return "";
		}
		$remaining = $buflen - $this->offset;
		if($remaining < $len){
			throw new InvalidArgumentException("Not enough bytes left in buffer: need $len, have $remaining");
		}

		return $len === 1 ? $this->buffer[$this->offset++] : substr($this->buffer, ($this->offset += $len) - $len, $len);
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function getRemaining() : string{
		$buflen = strlen($this->buffer);
		if($this->offset >= $buflen){
			throw new InvalidArgumentException("No bytes left to read");
		}
		$str = substr($this->buffer, $this->offset);
		$this->offset = $buflen;
		return $str;
	}

	public function put($str) : void{
		$this->buffer .= $str;
	}

	public function getBool() : bool{
		return $this->get(1) !== "\x00";
	}

	public function putBool(bool $v) : void{
		$this->buffer .= ($v ? "\x01" : "\x00");
	}

	public function getByte() : int{
		return ord($this->get(1));
	}

	public function putByte($v) : void{
		$this->buffer .= chr($v);
	}

	/**
	 * @return int
	 */
	public function getLong(){
		return Binary::readLong($this->get(8));
	}

	/**
	 * @param int $v
	 */
	public function putLong($v){
		$this->buffer .= Binary::writeLong($v);
	}

	public function getInt() : int{
		return Binary::readInt($this->get(4));
	}

	/**
	 * @param $v
	 */
	public function putInt($v){
		$this->buffer .= Binary::writeInt($v);
	}

	/**
	 * @return int
	 */
	public function getLLong(){
		return Binary::readLLong($this->get(8));
	}

	/**
	 * @param int $v
	 */
	public function putLLong($v){
		$this->buffer .= Binary::writeLLong($v);
	}

	/**
	 * @return int
	 */
	public function getLInt(){
		return Binary::readLInt($this->get(4));
	}

	/**
	 * @param $v
	 */
	public function putLInt($v){
		$this->buffer .= Binary::writeLInt($v);
	}

	/**
	 * @param $v
	 */
	public function putShort($v){
		$this->buffer .= Binary::writeShort($v);
	}

	public function getShort(){
		return Binary::readShort($this->get(2));
	}

	/**
	 * @return int
	 */
	public function getSignedShort(){
		return Binary::readSignedShort($this->get(2));
	}

	/**
	 * @param $v
	 */
	public function putSignedShort($v){
		$this->buffer .= Binary::writeShort($v);
	}

	public function getFloat(){
		return Binary::readFloat($this->get(4));
	}

	public function getRoundedFloat(int $accuracy){
		return Binary::readRoundedFloat($this->get(4), $accuracy);
	}

	/**
	 * @param $v
	 */
	public function putFloat($v){
		$this->buffer .= Binary::writeFloat($v);
	}

	/**
	 * @param bool $signed
	 *
	 * @return int
	 */
	public function getLShort($signed = true){
		return $signed ? Binary::readSignedLShort($this->get(2)) : Binary::readLShort($this->get(2));
	}

	public function getSignedLShort(){
		return Binary::readSignedLShort($this->get(2));
	}

	/**
	 * @param $v
	 */
	public function putLShort($v){
		$this->buffer .= Binary::writeLShort($v);
	}

	public function getLFloat(){
		return Binary::readLFloat($this->get(4));
	}

	public function getRoundedLFloat(int $accuracy){
		return Binary::readRoundedLFloat($this->get(4), $accuracy);
	}

	/**
	 * @param $v
	 */
	public function putLFloat($v){
		$this->buffer .= Binary::writeLFloat($v);
	}

	/**
	 * @return mixed
	 */
	public function getTriad(){
		return Binary::readTriad($this->get(3));
	}

	/**
	 * @param $v
	 */
	public function putTriad($v){
		$this->buffer .= Binary::writeTriad($v);
	}

	/**
	 * @return mixed
	 */
	public function getLTriad(){
		return Binary::readLTriad($this->get(3));
	}

	/**
	 * @param $v
	 */
	public function putLTriad($v){
		$this->buffer .= Binary::writeLTriad($v);
	}

	public function getString(){
		return $this->get($this->getUnsignedVarInt());
	}

	public function putString($v){
		$this->putUnsignedVarInt(strlen($v));
		$this->put($v);
	}

	public function getUUID() : UUID{
		//This is actually two little-endian longs: UUID Most followed by UUID Least
		$part1 = $this->getLInt();
		$part0 = $this->getLInt();
		$part3 = $this->getLInt();
		$part2 = $this->getLInt();
		return new UUID($part0, $part1, $part2, $part3);
	}

	public function putUUID(UUID $uuid){
		$this->putLInt($uuid->getPart(1));
		$this->putLInt($uuid->getPart(0));
		$this->putLInt($uuid->getPart(3));
		$this->putLInt($uuid->getPart(2));
	}

    /*
	public function getSlot() : Item{
		$id = $this->getVarInt();

		if($id <= 0){
			return Item::get(0, 0, 0);
		}
		$auxValue = $this->getVarInt();
		$data = $auxValue >> 8;
		if($data === 0x7fff){
			$data = -1;
		}
		$cnt = $auxValue & 0xff;

		$nbtLen = $this->getLShort();
		$nbt = "";

		if($nbtLen > 0){
			$nbt = $this->get($nbtLen);
		}

		$canPlaceOn = $this->getVarInt();
		if($canPlaceOn > 0){
			for($i = 0; $i < $canPlaceOn && !$this->feof(); ++$i){
				$this->getString();
			}
		}

		$canDestroy = $this->getVarInt();
		if($canDestroy > 0){
			for($i = 0; $i < $canDestroy && !$this->feof(); ++$i){
				$this->getString();
			}
		}

		return Item::get($id, $data, $cnt, $nbt);
	}



	public function putSlot(Item $item){
		if($item->getId() === 0){
			$this->putVarInt(0);

			return;
		}

		$this->putVarInt($item->getId());
		$auxValue = (($item->getDamage() & 0x7fff) << 8) | $item->getCount();
		$this->putVarInt($auxValue);
		$nbt = $item->getCompoundTag();
		$this->putLShort(strlen($nbt));
		$this->put($nbt);

		$this->putVarInt(0); //CanPlaceOn entry count (TODO)
		$this->putVarInt(0); //CanDestroy entry count (TODO)
	}
*/
	/**
	 * Reads an unsigned varint32 from the stream.
	 */
	public function getUnsignedVarInt() : int{
		return Binary::readUnsignedVarInt($this->buffer, $this->offset);
	}

	/**
	 * Writes an unsigned varint32 to the stream.
	 *
	 * @param $v
	 */
	public function putUnsignedVarInt(int $v){
		$this->put(Binary::writeUnsignedVarInt($v));
	}

	/**
	 * Reads a signed varint32 from the stream.
	 */
	public function getVarInt() : int{
		return Binary::readVarInt($this->buffer, $this->offset);
	}

	/**
	 * Reads a 64-bit variable-length integer from the buffer and returns it.
	 * @return int
	 */
	public function getUnsignedVarLong(){
		return Binary::readUnsignedVarLong($this->buffer, $this->offset);
	}

	/**
	 * Writes a 64-bit variable-length integer to the end of the buffer.
	 * @param int $v
	 */
	public function putUnsignedVarLong($v){
		$this->buffer .= Binary::writeUnsignedVarLong($v);
	}

	/**
	 * Reads a 64-bit zigzag-encoded variable-length integer from the buffer and returns it.
	 * @return int
	 */
	public function getVarLong(){
		return Binary::readVarLong($this->buffer, $this->offset);
	}

	/**
	 * Writes a 64-bit zigzag-encoded variable-length integer to the end of the buffer.
	 * @param int $v
	 */
	public function putVarLong($v){
		$this->buffer .= Binary::writeVarLong($v);
	}

	/**
	 * Writes a 32-bit zigzag-encoded variable-length integer to the end of the buffer.
	 */
	public function putVarInt($v) : void{
		$this->put(Binary::writeVarInt($v));
	}

	/**
	 * @return int
	 */
	public function getEntityId(){
		return $this->getVarInt();
	}

	/**
	 * @param $v
	 */
	public function putEntityId($v){
		$this->putVarInt($v);
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function getBlockCoords(&$x, &$y, &$z){
		$x = $this->getVarInt();
		$y = $this->getUnsignedVarInt();
		$z = $this->getVarInt();
	}

	/**
	 * Reads a block position with a signed Y coordinate.
	 * @param int &$x
	 * @param int &$y
	 * @param int &$z
	 */
	public function getSignedBlockCoords(&$x, &$y, &$z){
		$x = $this->getVarInt();
		$y = $this->getVarInt();
		$z = $this->getVarInt();
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function putBlockCoords($x, $y, $z){
		$this->putVarInt($x);
		$this->putUnsignedVarInt($y);
		$this->putVarInt($z);
	}

	/**
	 * Writes a block position with a signed Y coordinate.
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 */
	public function putSignedBlockCoords(int $x, int $y, int $z){
		$this->putVarInt($x);
		$this->putVarInt($y);
		$this->putVarInt($z);
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function getVector3f(&$x, &$y, &$z){
		$x = $this->getRoundedLFloat(4);
		$y = $this->getRoundedLFloat(4);
		$z = $this->getRoundedLFloat(4);
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 */
	public function putVector3f($x, $y, $z){
		$this->putLFloat($x);
		$this->putLFloat($y);
		$this->putLFloat($z);
	}

	/**
	 * Returns whether the offset has reached the end of the buffer.
	 * @return bool
	 */
	public function feof() : bool{
		return !isset($this->buffer[$this->offset]);
	}
}