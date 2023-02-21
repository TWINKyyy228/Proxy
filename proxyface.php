<?php

class Proxyface {

    public function __construct() {
        $this->lib = FFI::cdef("typedef _Bool (*onClientPacketSend)(char* payload, int len);
        typedef _Bool (*onServerPacketRecv)(char* payload, int len);
        typedef void (*onServerDisconnected)(int reason);
        typedef void (*onClientDisconnected)(int reason);
        typedef void (*voidfn) ();
        typedef struct hbval {
          _Bool enabled;
          float x;
          float y;
        } hbval;

        typedef struct tracerval {
          _Bool enabled;
          int part;
        } tracerval;

        extern void SubscribeOnClientPacketSend(onClientPacketSend fn);
        extern void SubscribeOnServerPacketRecv(onServerPacketRecv fn);
        extern void SubscribeOnServerDisconnected(onServerDisconnected fn);
        extern void SubscribeOnClientDisconnected(onClientDisconnected fn);
        extern void SetTicker(int interval, voidfn fun);
        extern void RunDelayed(int interval, voidfn fun);
        extern void SetNametag(int eid, char* name);
        extern void LiveTransfer(char* cip);
        extern void SetTracer(_Bool en, int part);
        extern void SetRPDownloadBypass(_Bool val);
        extern void SetClientID(long long int id);
        extern void SetHitbox(_Bool en, float x, float y);
        extern hbval GetHitbox();
        extern tracerval GetTracer();
        extern int GetPlayerUIDByEID();
        extern char* GetPlayerSkinBase64ByEid(unsigned int eid);
        extern void SetUUID(char* uuid);
        extern void SetSkinData(char* based);
        extern void SetInputMode(int mode);
        extern void SetDefaultInputMode(int mode);
        extern void SetUIProfile(int mode);
        extern void SetDeviceModel(char* model);
        extern void SetDeviceOS(int os);
        extern void SetNickname(char* nick);
        extern void SetSkinID(char* nick);
        extern void GenerateAndSaveClientID();
        extern void SetMovementSpeed(float speed);
        extern char* ConvertSkinBase64ToPngBase64(char* based);
        extern char* GetCurrentServerAddress();
        extern void StartProxy(char* cip);
        extern void StartProxyNonBlock(char* cip);
        extern void PutEnv(char* env);
        extern void SendToClient(char* cstr, int leng);
        extern void SendToServer(char* cstr, int leng);", './libproxy.so');
        $this->lib->PutEnv(json_encode(getenv()));
    }

    private $ocpse = null;
    public function subscribeOnClientPayloadSendEvent($func) {
        $this->ocpse = $func;
        $inst = $this;

        $this->lib->SubscribeOnClientPacketSend(function($ubytes, $len) use($inst) {
            return call_user_func($inst->ocpse, FFI::string($ubytes, $len), $len);
        });
    }

    private $ospre = null;
    public function subscribeOnServerPayloadRecvEvent($func) {
        $this->ospre = $func;
        $inst = $this;

        $this->lib->SubscribeOnServerPacketRecv(function($ubytes, $len) use($inst) {
            return call_user_func($inst->ospre, FFI::string($ubytes, $len), $len);
        });
    }

}