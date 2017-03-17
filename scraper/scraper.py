import platform
import string
from bs4 import BeautifulSoup
from selenium import webdriver

chrome_path = r"C:\Users\user\Desktop\chromedriver_win32\chromedriver.exe"
driver = webdriver.Chrome(chrome_path)
driver.get("https://watch.nba.com/game/20170311/TORMIA")
driver.find_element_by_xpath("""/html/body/div[7]/div[2]/div[3]/div[4]""").click()
driver.find_element_by_xpath("""/html/body/div[7]/div[2]/div[6]/div[1]/div/div[1]/div[1]/div[1]/div/div/span""").click()
items = driver.find_elements_by_class_name("items")

filename = "plays.csv"
f = open(filename, "w")

for item in items:
    itemtext = item.text
    itemparts = string.split(itemtext, "\n")
    time = itemparts[0]
    team = itemparts[1]
    score = itemparts[2]
    play = itemparts[3]
    f.write(time + "," + team + "," + score + "," + play + "\n")

f.close()
driver.close()
