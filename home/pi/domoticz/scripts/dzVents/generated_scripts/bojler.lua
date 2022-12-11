-- bojler

-- script se snazi vyhrivat boiler, ale jen pokud neni jiz nahraty 
-- a je k tomu pripravy krb a dostatecne vyhrata akumulacka

-- ******************** MENITELNE PARAMETRY: ***************************************************************

local max_bojler=58
local min_bojler=57
local max_akumulacka=59
local min_akumulacka=58


return {
	on = {
		devices = {
			'akumulacka_dolni',
			'krb',
			'bojler'
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
            Switchs={ 'bojler_sw'},
            Temperatures={'akumulacka_dolni', 'krb','bojler'},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( 
            dev['bojler']<min_bojler and 
            dev['krb']>dev['bojler'] and 
            dev['akumulacka_dolni']>max_akumulacka
            ) then
                dev['bojler_sw']='On'
        elseif (
            dev['bojler']>max_bojler or
            dev['krb']<dev['bojler'] or
            dev['akumulacka_dolni']<min_akumulacka
            ) then
                dev['bojler_sw']='Off'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
	end
}