-- pro testovani se hodi tato adresa:
-- http://localhost:8080/json.htm?type=command&param=udevice&idx=18&nvalue=0&svalue=100

-- EXAMPLES 
-- TIME:
--    domoticz.devices('Device_name').lastUpdate.millisecondsAgo
--                                               secondsAgo
--                                               minutesAgo
--                                               daysAgo
--    Cas se uklada to objektu typu time - lepe se s nim pak pracuje
--    local Time = require('Time')
--    local now = Time() -- current time
--    local someTime = Time('2017-12-31 22:19:15')

return {
        helpers = {
            -- globalni helpery (pomocne funkce)
            --   z programum se k nim pak pristupuje:
            --      domoticz.helpers.nazev_funkce(parametry funkce)
            
            -- pokus
            --myAdd = function(text)
            --    return ( text .. "ahoj")
            --end
            --,
            -- funkce ktera privetive vypise obsah tabulky (vhodne pro ucely ladeni)
            dump = function(domoticz,o)
                if type(o) == 'table' then
                    local s = '{ '
                    for k,v in pairs(o) do
                        if type(k) ~= 'number' then k = '"'..k..'"' end
                        s = s .. '['..k..'] = ' .. domoticz.helpers.dump(domoticz,v) .. ', '
                    end
                    return s .. '} '
                else
                    return tostring(o)
                end
            end
            ,
            -- funkce pro vypsani logu 
            -- Log se vypise pouze pokud je zaply switch 'Debug'
            -- nepovinny parametr offset udava kolik se prida mezer pred samotny text (osazovani textu, pro lepsi citelnost logu)
            log = function(domoticz, text, offset, level)
                offset = offset or 0 -- default hodnota pro offset
                level = level or domoticz.LOG_INFO -- default hodnota pro log level

                if ( domoticz.devices('Debug').state == 'On' ) then
                    odsazeni=''
                    for i=1,offset do
                        odsazeni=odsazeni.." "
                    end
                    domoticz.log(odsazeni..text, level)
                end
            end
            ,
            split = function(text)
                arr={}
                i=0
                for str in string.gmatch(text, "%S+") do
                    arr[i]=str
                    i=i+1
                end
                return arr
            end
            ,
            -- funkce pro zapis do souboru (pouzivam pro uchovani dulezitych udaju behem dlouhodobeho ladeni)
            -- obsahuje kontrolu max. velikosti souboru
            write_log = function(text)
                file = io.open("/tmp/domoticz_write_log.txt", "a")
                size = file:seek("end")
                if ( size > 50000 ) then
                    file:close()
                    os.remove("/tmp/domoticz_write_log.old.txt")                                    -- pokud existuje tak stary zalozni logovaci soubor smaz
                    os.rename ("/tmp/domoticz_write_log.txt", "/tmp/domoticz_write_log.old.txt")    -- aktualni logovaci soubor prejmenuj na zalozni
                    file = io.open("/tmp/domoticz_write_log.txt", "a")                              -- otevri novy sobor pro zapis
                end
                local Time = require('Time')
                local now = Time() -- current time
                file:write(now.getISO())
                file:write(" : ")
                file:write(text)
                file:write("\n")
                file:close()
            end
            ,
            -- funkce pro inicializaci promennych zatupujicich deveices a variables
            -- sjednocuje to jinak trochu pomerne roztristene api dzVents a tim se vysledny program stava citelnejsi
            -- used dev je pole s definyci pouzivancych zarizani a promennych
            -- ve tvaru :
            -- used={
            --         Switchs={'Debug'},
            --         Temperatures={'Tep_AKU_2'},
            --         SetPoints={'Termostat_podlaha'},
            --         Variables={}
            --    }
            --  dev={} a var={} jsou globalni promenne poli, ktere tato fce naplni
            ini = function(domoticz, used)
                dev={}
                var={}
                for i,name in ipairs(used['Switchs']) do
                    if ( domoticz.devices(name).state == 'On' ) then dev[name]='On' end
                    if ( domoticz.devices(name).state == 'Off' ) then dev[name]='Off' end
                end
                for i,name in ipairs(used['Temperatures']) do
                    dev[name]=domoticz.devices(name).temperature
                end
                for i,name in ipairs(used['SetPoints']) do
                    dev[name]=domoticz.devices(name).setPoint
                end
                for i,name in ipairs(used['Variables']) do
                    var[name]=domoticz.variables(name).value
                end
                for i,name in ipairs(used['Devices']) do
                    --log("device:"..name.." type:"..domoticz.devices(name).deviceType.." subType:"..domoticz.devices(name).deviceSubType)
                    -- temperature
                    if ( domoticz.devices(name).deviceType == 'Temp' ) then
                        dev[name]=domoticz.devices(name).temperature
                    end
                    if ( domoticz.devices(name).deviceType == 'Light/Switch' ) then
                        --log("Light/Switch")
                        -- switch
                        if( domoticz.devices(name).deviceSubType == 'Switch' ) then
                            --log("Switch")
                            log("Switch svalue["..name.."]:"..domoticz.devices(name).sValue )
                            if ( string.find(domoticz.devices(name).sValue, "Set Level:")==1 ) then
                                dev[name]=tonumber(domoticz.helpers.split(domoticz.devices(name).sValue)[2])  -- Set Level: 60 %
                            else
                                if ( domoticz.devices(name).state=='Off' ) then
                                    dev[name]=0
                                end
                            end
                        end
                        -- slector switch
                        if ( domoticz.devices(name).deviceSubType == 'Selector Switch' ) then
                            --log("Selector Switch")
                            --log("svalue:"..domoticz.devices(name).sValue)
                            dev[name]=tonumber(domoticz.helpers.split(domoticz.devices(name).sValue)[2])  -- Set Level: 60 %
                        end
                    end
                    -- color switch
                    if ( domoticz.devices(name).deviceType == 'Color Switch' ) then
                        log("svalue:"..domoticz.devices(name).sValue)
                        dev[name]=tonumber(domoticz.helpers.split(domoticz.devices(name).sValue)[2])  -- Set Level: 60 %
                    end
                    -- termostat
                    if ( domoticz.devices(name).deviceType == 'Thermostat' ) then
                        dev[name]=domoticz.devices(name).setPoint
                    end
                    if ( domoticz.devices(name).deviceType == 'General' ) then
                        -- percentage
                        if ( domoticz.devices(name).deviceSubType == 'Percentage' ) then
                            dev[name]=domoticz.devices(name).percentage
                        end
                    end
                    if ( domoticz.devices(name).deviceType == 'Usage' ) then
                        dev[name]=domoticz.devices(name).actualWatt
                    end
                end
                -- domoticz.log(domoticz.helpers.dump(domoticz,dev)) -- vypsani vstupnich hodnot do scriptu pro ucely lepsiho ladeni (generuje toho hodne, tak pokud to neni potreba tak to zakomentovavam)
                -- domoticz.log(domoticz.helpers.dump(domoticz,var)) -- vypsani vstupnich hodnot do scriptu pro ucely lepsiho ladeni (generuje toho hodne, tak pokud to neni potreba tak to zakomentovavam)
                return dev,var
            end
            ,
            -- funkce pro ukonceni promennych zastupujicich devices a variables ( pokud se lisi od aktualnich hodnot tak budou aktualizavany, jinak ne.
            -- Timto se rapidne snizuje, pocet udalosti, ktere by jinak nastavaly pokud by se se hodnoty aktualizovaly i kdyz to neni potreba )
            ending = function(domoticz, used, dev, var)
                log("----- ZAVERECNE VYHONOCENI -----")
                for i,name in ipairs(used['Switchs']) do
                    if ( domoticz.devices(name).state~=dev[name] ) then
                        if ( dev[name]=='On') then domoticz.devices(name).switchOn() end
                        if ( dev[name]=='Off') then domoticz.devices(name).switchOff() end
                        domoticz.log("name:"..name)
                        domoticz.log(name.."="..dev[name])
                    end
                end
                for i,name in ipairs(used['SetPoints']) do
                    if ( dev[name]~=domoticz.devices(name).setPoint ) then 
                        domoticz.devices(name).updateSetPoint(dev[name]) 
                        domoticz.log(name.."="..dev[name])
                    end
                end
                for i,name in ipairs(used['Variables']) do
                    if ( var[name]~=domoticz.variables(name).value ) then
                        domoticz.variables(name).set(var[name])
                        domoticz.log(name.."="..var[name])
                    end
                end
                -- totez jako vyse ale trochu univerzalneji, do pole Devices muzu dat jakekoliv podporovane zarizeni a na konci bude dle sveho typu aktualizovane
                -- ma to i chybky  napr. u teploty se ciste teoreticky muze zmenit hodnota i behem poravadeni scriptu a toto funkce pak nechtene vrati honotu z pocatku scriptu
                -- proto nechavam i strasi zpusob ukonceni na zaklade pole Switchs, SetPoints
                for i,name in ipairs(used['Devices']) do
                    -- temperature
                    if ( domoticz.devices(name).deviceType == 'Temp' ) then
                        if (domoticz.devices(name).temperature~=dev[name] ) then
                            domoticz.devices(name).updateTemperature(dev[name])
                            domoticz.log(name.."="..dev[name])
                        end
                    end
                    if ( domoticz.devices(name).deviceType == 'Light/Switch' ) then
                        -- switch
                        if( domoticz.devices(name).deviceSubType == 'Switch' ) then
                            if ( dev[name]=='On' or dev[name]=='Off') then
                                if ( domoticz.devices(name).state~=dev[name] ) then
                                    if ( dev[name]=='On') then domoticz.devices(name).switchOn() end
                                    if ( dev[name]=='Off') then domoticz.devices(name).switchOff() end
                                    domoticz.log(name.."="..dev[name])
                                end
                            elseif ( type(dev[name]) == "number" ) then
                                if ( domoticz.devices(name).state=='Off' ) then value=0
                                else value=tonumber(domoticz.helpers.split(domoticz.devices(name).sValue)[2]) end
                                if ( value~= dev[name] ) then
                                    domoticz.devices(name).dimTo(dev[name])
                                    domoticz.log(name.."="..dev[name])
                                end
                            end
                        end
                        -- slector switch
                        if ( domoticz.devices(name).deviceType == 'Selector Switch' ) then
                            if ( domoticz.devices(name).state=='Off' ) then value=0
                            else value=tonumber(domoticz.helpers.split(domoticz.devices(name).sValue)[2]) end
                            if ( value~= dev[name] ) then
                                domoticz.devices(name).setLevel(dev[name])
                                domoticz.log(name.."="..dev[name])
                            end
                        end
                    end
                    -- color switch
                    if ( domoticz.devices(name).deviceType == 'Color Switch' ) then
                        if ( tonumber(domoticz.helpers.split(domoticz.devices(name).sValue)[2])~=dev[name] ) then
                            domoticz.devices(name).setLevel(dev[name])
                            domoticz.log(name.."="..dev[name])
                        end
                    end
                    -- termostat
                    if ( domoticz.devices(name).deviceType == 'Thermostat' ) then
                        if ( domoticz.variables(name).setPoint~=dev[name] ) then
                            domoticz.devices(name).updateSetPoint(dev[name]) 
                            domoticz.log(name.."="..dev[name])
                        end
                    end
                    if ( domoticz.devices(name).deviceType == 'General' ) then
                        -- percentage
                        if ( domoticz.devices(name).deviceSubType == 'Percentage' ) then
                            if ( domoticz.devices(name).percentage~=dev[name] ) then
                                domoticz.devices(name).updatePercentage(dev[name])
                                domoticz.log(name.."="..dev[name])
                            end
                        end
                    end
                    if ( domoticz.devices(name).deviceType == 'Usage' ) then
                        if ( domoticz.devices(name).actualWatt~=dev[name] ) then
                            domoticz.devices(name).updateEnergy(dev[name])
                            domoticz.log(name.."="..dev[name])
                        end
                    end
                end
                log("End")
            end
        },
    
        data = {
            -- ukazka globalnich data
            -- z programu se pak k nim pristupuje:
            --   domoticz.globalData.nazev_promenne
            -- napr:
            -- counter = { initial = 20 },
            -- heatingProgramActive = { initial = false }
        }
    }
