-- alarm

-- script sleduje teploty akumulacky a krbu, kdyz prekroci zadanou mez tak spusti pipani
-- kdyz teploty klesnou pipanu vypne

-- ******************** MENITELNE PARAMETRY: ***************************************************************

local max_akumulacka=80
local max_krb=85

return {
	on = {
		devices = {
			'akumulacka_dolni',
			'krb'
		}
	},
	logging = {
		level = domoticz.LOG_INFO, -- mozne urovne logovani jsou domoticz.LOG_INFO, domoticz.LOG_MODULE_EXEC_INFO, domoticz.LOG_DEBUG or domoticz.LOG_ERROR . domoticz.LOG_ERROR vypne logovani pro tento script
		marker = 'alarm',
	},
	execute = function(domoticz, triggeredItem)
		domoticz.log('Device ' .. triggeredItem.name .. ' was changed', domoticz.LOG_INFO)
        --log=domoticz.log
        log = function(text, offset, level)
            domoticz.helpers.log(domoticz, text, offset, level)
        end

	    used={
            Switchs={ 'beep'},
            Temperatures={'akumulacka_dolni', 'krb'},
            SetPoints={},
            Variables={},
            Devices={}
        }

        log ('Start')
        dev, var = domoticz.helpers.ini(domoticz, used)
        if ( dev['akumulacka_dolni']>max_akumulacka or dev['krb']>max_krb ) then
            dev['beep']='On'
        elseif ( dev['akumulacka_dolni']<max_akumulacka and dev['krb']<max_krb ) then
            dev['beep']='On'
        end
        domoticz.helpers.ending(domoticz, used, dev, var)
        
	end
}