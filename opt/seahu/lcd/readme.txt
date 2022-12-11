graph_lcd.service, beep.service a lcd_menu.service - je potreba prekopirovat do /lib/systemd/system/ cimz se se srciptu
            vytvori sluzba. Tu je potreba aktivovat nasledujici prikazy:
            sudo systemctl daemon-reload

            sudo systemctl enable graph_lcd.service
            sudo systemctl start graph.service

            sudo systemctl enable beep.service
            sudo systemctl start beep.service

            sudo systemctl enable lcd_menu.service
            sudo systemctl start lcd_menu.service


PS: umisteni predpokladam v adresari /opt/inventor