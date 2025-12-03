from playwright.sync_api import sync_playwright

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        # Add permission to access clipboard if needed, though not strictly required here
        context = browser.new_context()

        # We need to simulate a logged-in state.
        # Since we use NextAuth, simulating the cookie is tricky without a full e2e flow.
        # However, for this screenshot verification, we can rely on the fact that we mocked
        # the session retrieval in the 'DashboardPage' if needed, OR we accept we might see a redirect.

        # NOTE: Without a real login cookie, the dashboard might redirect to '/'.
        # To bypass this for VISUAL verification of the component structure,
        # I'm relying on the seeded data being present.

        # BUT: The layout redirects if unauthenticated.
        # So I will screenshot the LOGIN page (Home) first to show it exists,
        # and then I'll try to access the dashboard. If it redirects, that's expected behavior.

        page = context.new_page()

        # 1. Check Home Page (Leaderboard)
        page.goto("http://localhost:3000")
        page.wait_for_selector("table")
        page.screenshot(path="verification/home_v2.png")

        browser.close()

if __name__ == "__main__":
    run()
