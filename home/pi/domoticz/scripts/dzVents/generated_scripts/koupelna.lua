-- koupelna

-- script se snazi vyhrivat koupelnu (proc se testuje teplotni cidlo loznice - nevim)
-- vyhriva se dokud neni nahrata na pozadovanou teplotu loznice a zaroven pokud je nahrata akumulacka
-- nebo bez ohledu na to jak moc je nahrata loznice se koupena vyhriva v zadany cas pokud je dostatecne nahraty krb 

-- ******************** MENITELNE PARAMETRY: ***************************************************************

local max_loznice=20.8
local min_loznice=20.3
local max_akumulacka=35
local min_akumulacka=30
local min_krb=55
local od_do='at 15:30-18:45' -- musi byt ve tvaru 'at hh:mm-hh:mm'



return {
	on = {
		devices = {
			'akumulacka_horni',
			'krb',
			'loznice'
		}
	},
	logging = {
		level = domoticz.LOG_INFO, -- mozne urovne logovani jsou domoticz.LOG_INFO, domoticz.LOG_MODULE_EXEC_INFO, domoticz.LOG_DEBUG or domoticz.LOG_ERROR . domoticz.LOG_ERROR vypne logovani pro tento script
		marker = 'bojler',
	},
	execute = function(domoticz, triggeredItem)
		domoticz.log('Device ' .. triggeredItem.name .. ' was changed', domoticz.LOG_INFO)
        --log=domoticz.log
        log = function(text, offset, level)
            domoticz.helpers.log(domoticz, text, offset, level)
        end

	    used={
            Switchs={ 'rozdelovac_koupelna','krb_sw'},
            Temperatures={'akumulacka_horni', 'krb','loznice'},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( 
                ( dev['akumulacka_horni']>max_akumulacka and dev['loznice']<min_loznice ) or
                (
                dev['krb']>min_krb and
                dev['krb_sw']=='On' and
                domoticz.time.matchesRule(od_do)==true
                )
            ) then
                dev['rozdelovac_koupelna']='On'
        elseif (
                ( dev['akumulacka_horni']<min_akumulacka or dev['loznice']>max_loznice ) and
                (
                dev['krb']<min_krb or
                dev['krb_sw']=='Off' or
                domoticz.time.matchesRule(od_do)==false
                )
            ) then
                 dev['rozdelovac_koupelna']='Off'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
	end
}