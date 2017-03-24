import platform
import datetime
import string
import os.path 
from bs4 import BeautifulSoup as soup
from selenium import webdriver

def scrapeGames(gameIDs):
    for gameID in gameIDs:
        print 'g'
        chrome_path = r"C:\Users\user\Desktop\chromedriver_win32\chromedriver.exe"
        driver = webdriver.Chrome(chrome_path)
        driver.get("http://stats.nba.com/game/#!/00" + str(gameID) + "/playbyplay")
        html_source = driver.page_source
        pagesoup = soup(html_source, 'html.parser')
        
        datediv = pagesoup.find("div", {"class" : "game-summary__date"})
        date = datetime.datetime.strptime(datediv.text, "%b %d, %Y")
        dateform = date.strftime('%Y%m%d')
        twoteams = pagesoup.findAll("td", {"class" : "team-name"})

        driver2 = webdriver.Chrome(chrome_path)
        driver2.get("https://watch.nba.com/game/"+dateform+"/"+twoteams[0].text + twoteams[1].text)
        driver2.find_element_by_xpath("""/html/body/div[7]/div[2]/div[3]/div[2]""").click()
        homefirstnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("first-name")))
        homelastnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("last-name")))

        driver2.find_element_by_class_name("away-team-id").click()
        awayfirstnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("first-name")))
        awaylastnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("last-name")))

        filename = "games/" + str(gameID) + "-" + dateform + "-" + twoteams[0].text + twoteams[1].text + ".csv"
        if os.path.isfile(filename):
            exit
        f = open(filename, "w")

        print("csv '" + filename + "' created")
        
        for i in range(0, len(awaylastnames)):
            if(i > 0):
                f.write(",")
            f.write(awayfirstnames[i] + "," + awaylastnames[i])
        f.write("\n")
        for i in range(0, len(homelastnames)):
            if(i > 0):
                f.write(",")
            f.write(homefirstnames[i] + "," + homelastnames[i])
        f.write("\n")

        import re

        def supertrim(string):
            return re.sub('\s+',' ',string).strip()

        pbpdiv = pagesoup.find("div", { "class" : "boxscore-pbp__inner ng-scope" } )
        pbptrs = pbpdiv.table.tbody.findAll("tr")
        for index, pbptr in enumerate(pbptrs):
            tds = pbptr.findAll("td")
            if len(tds) == 1:
                try:
                    f.write(supertrim(tds[0].text.encode()))
                except UnicodeEncodeError:
                    print ("Unicode encode error at singular cell at line "+str(index)+1)
                    print ("'"+tds[0].text+"'")
                except:
                    print "Unexpected error:", sys.exc_info()[0]  
                f.write("\n")
                continue
            
            if tds[1] is not None:
                try:
                    f.write(supertrim(tds[1].text.encode()))
                except UnicodeEncodeError:
                    print ("Unicode encode error at time/score cell at line "+str(index)+1)
                    print ("'"+tds[1].text+"'")
                except:
                    print "Unexpected error:", sys.exc_info()[0]                    
            f.write(",")
            
            if tds[0] is not None:
                try:
                    f.write(supertrim(tds[0].text.encode()))
                except UnicodeEncodeError:
                    print ("Unicode encode error at away cell at line "+str(index)+1)
                    print ("'"+tds[0].text+"'")
                except:
                    print "Unexpected error:", sys.exc_info()[0]  
            f.write(",")

            if tds[2] is not None:
                try:
                    f.write(supertrim(tds[2].text.encode()))
                except UnicodeEncodeError:
                    print ("Unicode encode error at home cell at line "+str(index)+1)
                    print ("'"+tds[2].text+"'")
                except:
                    print "Unexpected error:", sys.exc_info()[0]              
            f.write("\n")

        f.write("***End of scrape")
                
        driver.quit()
        driver2.quit()
        f.close()

scrapeGames(range(21600056, 21600066))
