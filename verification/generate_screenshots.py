from playwright.sync_api import sync_playwright

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        # 1. Admin Dashboard (Notifications)
        page.goto("http://localhost:3000/dashboard")
        page.wait_for_selector("h1") # Wait for title
        page.screenshot(path="verification/dashboard_admin.png")
        print("Captured Dashboard")

        # 2. Challenge Page
        page.goto("http://localhost:3000/dashboard/ask")
        page.wait_for_selector("input[name='q']")
        page.screenshot(path="verification/dashboard_ask.png")
        print("Captured Ask")

        # 3. Report Page
        page.goto("http://localhost:3000/dashboard/report")
        page.wait_for_selector("button")
        page.screenshot(path="verification/dashboard_report.png")
        print("Captured Report")

        browser.close()

if __name__ == "__main__":
    run()
