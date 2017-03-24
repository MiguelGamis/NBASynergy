import platform
import datetime
import string
import os.path 
from bs4 import BeautifulSoup as soup
from selenium import webdriver

def scrapeShots(gameIDs):
    for gameID in gameIDs:
        chrome_path = r"C:\Users\user\Desktop\chromedriver_win32\chromedriver.exe"
        driver = webdriver.Chrome(chrome_path)
        driver.get("http://stats.nba.com/game/#!/00" + str(gameID) + "/shotchart")
        html_source = driver.page_source
        pagesoup = soup(html_source, 'html.parser')

        datediv = pagesoup.find("div", {"class" : "game-summary__date"})
        date = datetime.datetime.strptime(datediv.text, "%b %d, %Y")
        dateform = date.strftime('%Y%m%d')
        twoteams = pagesoup.findAll("td", {"class" : "team-name"})

        filename = "shots/" + str(gameID) + "-" + dateform + "-" + twoteams[0].text + twoteams[1].text + ".csv"
        if os.path.isfile(filename):
            exit
        f = open(filename, "w")

        f.write(twoteams[0].text + "\n")

        f.write("Period" + ",")
        f.write("Clock" + ",")
        f.write("Made" + ",")
        f.write("Player name" + ",")
        f.write("Player ID" + ",")
        f.write("X" + ",")
        f.write("Y" + "\n")
        
        shotgroups = pagesoup.findAll("g", { "class" : "shotplot__shots" } )
        
        for shot in shotgroups[0].findAll("g", { "class" : "shotplot__shot" } ):
            f.write(shot['data-period'] + ",")
            f.write(shot['data-clock'] + ",")
            f.write(shot['data-madeflag'] + ",")
            f.write(shot['data-player-name'] + ",")
            f.write(shot['data-player-id'] + ",")
            f.write(shot['data-x'] + ",")
            f.write(shot['data-y'] + "\n")

        f.write(twoteams[1].text + "\n")

        f.write("Period" + ",")
        f.write("Clock" + ",")
        f.write("Made" + ",")
        f.write("Player name" + ",")
        f.write("Player ID" + ",")
        f.write("X" + ",")
        f.write("Y" + "\n")

        for shot in shotgroups[1].findAll("g", { "class" : "shotplot__shot" } ):
            f.write(shot['data-period'] + ",")
            f.write(shot['data-clock'] + ",")
            f.write(shot['data-madeflag'] + ",")
            f.write(shot['data-player-name'] + ",")
            f.write(shot['data-player-id'] + ",")
            f.write(shot['data-x'] + ",")
            f.write(shot['data-y'] + "\n")

        f.write("***End of scrape***")
        f.close()
        print("shots csv '" + filename + "' created")

        driver.quit()

        driver2 = webdriver.Chrome(chrome_path)
        driver2.get("https://watch.nba.com/game/"+dateform+"/"+twoteams[0].text + twoteams[1].text)
        driver2.find_element_by_xpath("""/html/body/div[7]/div[2]/div[3]/div[2]""").click()
        homefirstnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("first-name")))
        homelastnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("last-name")))

        driver2.find_element_by_class_name("away-team-id").click()
        awayfirstnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("first-name")))
        awaylastnames = list(map(lambda x: x.text, driver2.find_elements_by_class_name("last-name")))

        filename = "playbyplays/" + str(gameID) + "-" + dateform + "-" + twoteams[0].text + twoteams[1].text + ".csv"
        if os.path.isfile(filename):
            exit
        f = open(filename, "w")

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

        driver2.find_element_by_xpath("""/html/body/div[7]/div[2]/div[3]/div[4]""").click()
        driver2.find_element_by_xpath("""/html/body/div[7]/div[2]/div[6]/div[1]/div/div[1]/div[1]/div[1]/div/div""").click()

        html_source2 = driver2.page_source
        pagesoup2 = soup(html_source2, 'html.parser')
        periods = pagesoup2.findAll("div", { "class" : "playbyplay-content show" } )

        for period in reversed(periods):
            eventdivs = period.contents
            for eventdiv in reversed(eventdivs):
                if eventdiv['class'] == "start":
                    f.write(eventdiv.text + "\n")
                elif eventdiv['class'] == "time-out":
                    f.write(eventdiv.text + "\n")
                elif eventdiv['class'] == "items":
                    event = eventdiv.find("div")
                    gameinfo = event.find("div", {"class" : "game-info"})
                    time = gameinfo.find("div", {"class" : "time"}).text
                    team = gameinfo.find("div", {"class" : "team"}).text
                    score = gameinfo.find("div", {"class" : "record-score"}).text
                    playerinfo = event.find("div", {"class" : "player-info"})
                    player = playerinfo.find("div", {"class" : "player-left"})
                    playerlink = player.find("a")
                    href = playerlink['href']
                    firstname = ""
                    lastname = ""
                    playerid = ""
                    if href is not None:
                        hrefparts = href.split("/")
                        firstname = hrefparts[-3]
                        lastname = hrefparts[-2]
                        playerid = hrefparts[-1]
                    play = playerinfo.find("div", {"class" : "player-right"})
                    playdesc = play.find("div", {"class" : "desc"}).text

                    f.write(time + "," + score + "," + team + "," + playerid + "," + firstname + "," + lastname + "," + playdesc + "\n");
        f.write("***End of scrape***")
        f.close()
        print("playbyplays csv '" + filename + "' created")

        driver2.quit()
                    
scrapeShots(range(21600001, 21600002))
