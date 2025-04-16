
--tạo id_account
DELIMITER $$

CREATE TRIGGER before_insert_accounts
BEFORE INSERT ON Accounts
FOR EACH ROW
BEGIN
    DECLARE next_id INT;

    -- Kiểm tra nếu bảng rỗng thì gán id_account = 1
    IF NOT EXISTS (SELECT 1 FROM Accounts) THEN
        SET NEW.id_account = 1;
    ELSE
        -- Tìm giá trị id_account nhỏ nhất bị khuyết
        SELECT MIN(t1.id_account + 1) INTO next_id
        FROM Accounts t1
        WHERE NOT EXISTS (SELECT 1 FROM Accounts t2 WHERE t2.id_account = t1.id_account + 1);

        -- Nếu không có giá trị khuyết, gán id_account = MAX(id_account) + 1
        IF next_id IS NULL THEN
            SELECT MAX(id_account) + 1 INTO next_id FROM Accounts;
        END IF;

        -- Gán giá trị mới cho id_account
        SET NEW.id_account = next_id;
    END IF;
END $$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER before_insert_delivery
BEFORE INSERT ON Delivery_Information
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng trống, bắt đầu từ DEL1
    IF NOT EXISTS (SELECT 1 FROM Delivery_Information WHERE id_delivery LIKE 'DEL%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm giá trị nhỏ nhất bị thiếu
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num -- Để tìm thiếu số 1
            UNION
            SELECT CAST(SUBSTRING(id_delivery, 4) AS UNSIGNED) AS id_num
            FROM Delivery_Information
            WHERE id_delivery LIKE 'DEL%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Delivery_Information
            WHERE CAST(SUBSTRING(id_delivery, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị mới cho id_delivery
    SET new_id = CONCAT('DEL', next_id);
    SET NEW.id_delivery = new_id;
END $$

DELIMITER ;


DELIMITER //
CREATE TRIGGER set_shipping_fee
BEFORE INSERT ON Delivery_Information
FOR EACH ROW
BEGIN
    IF NEW.city = 'Cần Thơ' THEN
        SET NEW.shipping_fee = 15000;
    ELSE
        SET NEW.shipping_fee = 30000;
    END IF;
END;
//
DELIMITER ;

DELIMITER $$

CREATE TRIGGER before_insert_orders
BEFORE INSERT ON Orders
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Kiểm tra xem đã có order nào với id_account này chưa
    IF NOT EXISTS (
        SELECT 1 FROM Orders WHERE id_account = NEW.id_account
    ) THEN
        -- Nếu chưa có, gán id_order = 'REORD' + id_account
        SET new_id = CONCAT('REORD', NEW.id_account);
    ELSE
        -- Nếu đã có, tiếp tục tạo id_order dạng ORD + số nhỏ nhất bị thiếu
        IF NOT EXISTS (SELECT 1 FROM Orders WHERE id_order LIKE 'ORD%') THEN
            SET next_id = 1;
        ELSE
            SELECT MIN(t1.id_num + 1) INTO next_id
            FROM (
                SELECT 0 AS id_num
                UNION
                SELECT CAST(SUBSTRING(id_order, 4) AS UNSIGNED) AS id_num
                FROM Orders
                WHERE id_order LIKE 'ORD%'
            ) t1
            WHERE NOT EXISTS (
                SELECT 1 FROM Orders
                WHERE CAST(SUBSTRING(id_order, 4) AS UNSIGNED) = t1.id_num + 1
            );
        END IF;

        SET new_id = CONCAT('ORD', next_id);
    END IF;

    SET NEW.id_order = new_id;
END $$

DELIMITER ;


-- tạo id_cancel tự động
DELIMITER $$

CREATE TRIGGER before_insert_order_cancellations
BEFORE INSERT ON Order_Cancellations
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng chưa có CAN nào, bắt đầu từ CAN1
    IF NOT EXISTS (SELECT 1 FROM Order_Cancellations WHERE id_cancel LIKE 'CAN%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm giá trị nhỏ nhất bị thiếu trong chuỗi CAN
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num -- Để tìm thiếu số 1
            UNION
            SELECT CAST(SUBSTRING(id_cancel, 4) AS UNSIGNED) AS id_num
            FROM Order_Cancellations
            WHERE id_cancel LIKE 'CAN%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Order_Cancellations
            WHERE CAST(SUBSTRING(id_cancel, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị id_cancel mới
    SET new_id = CONCAT('CAN', next_id);
    SET NEW.id_cancel = new_id;
END $$

DELIMITER ;

-- tạo id_activity tự động
DELIMITER $$

CREATE TRIGGER before_insert_activity_history
BEFORE INSERT ON Activity_History
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng chưa có ACT nào, bắt đầu từ ACT1
    IF NOT EXISTS (SELECT 1 FROM Activity_History WHERE id_activity LIKE 'ACT%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm giá trị nhỏ nhất bị thiếu trong chuỗi ACT
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num
            UNION
            SELECT CAST(SUBSTRING(id_activity, 4) AS UNSIGNED) AS id_num
            FROM Activity_History
            WHERE id_activity LIKE 'ACT%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Activity_History
            WHERE CAST(SUBSTRING(id_activity, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị id_activity mới
    SET new_id = CONCAT('ACT', next_id);
    SET NEW.id_activity = new_id;
END $$

DELIMITER ;

--kiểm tra thời gian khuyến mãi
DELIMITER //

CREATE EVENT IF NOT EXISTS evt_update_expired_promotions
ON SCHEDULE
    EVERY 1 DAY
    STARTS TIMESTAMP(CURRENT_DATE + INTERVAL 1 DAY)  -- bắt đầu từ 00h ngày mai
DO
BEGIN
    UPDATE Products
    SET id_promotion = NULL
    WHERE id_promotion IS NOT NULL
      AND id_promotion IN (
        SELECT id_promotion FROM Promotions
        WHERE end_day < CURDATE()
      );
END;
//

DELIMITER ;

--tạo id_contact tự động
DELIMITER $$

CREATE TRIGGER before_insert_contacts
BEFORE INSERT ON Contacts
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu chưa có bản ghi nào, bắt đầu từ CONT1
    IF NOT EXISTS (SELECT 1 FROM Contacts WHERE id_contact LIKE 'CONT%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm số nhỏ nhất bị thiếu trong chuỗi CONTx
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num
            UNION
            SELECT CAST(SUBSTRING(id_contact, 5) AS UNSIGNED) AS id_num
            FROM Contacts
            WHERE id_contact LIKE 'CONT%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Contacts
            WHERE CAST(SUBSTRING(id_contact, 5) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán id_contact mới
    SET new_id = CONCAT('CONT', next_id);
    SET NEW.id_contact = new_id;
END $$

DELIMITER ;

--tạo id_supplier tự động
DELIMITER $$

CREATE TRIGGER before_insert_supplier
BEFORE INSERT ON Suppliers
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng chưa có SUP nào, bắt đầu từ SUP1
    IF NOT EXISTS (SELECT 1 FROM Suppliers WHERE id_supplier LIKE 'SUP%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm số nhỏ nhất bị thiếu trong chuỗi SUP
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num
            UNION
            SELECT CAST(SUBSTRING(id_supplier, 4) AS UNSIGNED) AS id_num
            FROM Suppliers
            WHERE id_supplier LIKE 'SUP%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Suppliers
            WHERE CAST(SUBSTRING(id_supplier, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị id_supplier mới
    SET new_id = CONCAT('SUP', next_id);
    SET NEW.id_supplier = new_id;
END $$

DELIMITER ;

--tạo id_receipt tự động
DELIMITER $$

CREATE TRIGGER before_insert_receipt
BEFORE INSERT ON Product_receipt
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng chưa có REC nào, bắt đầu từ REC1
    IF NOT EXISTS (SELECT 1 FROM Product_receipt WHERE id_receipt LIKE 'REC%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm số nhỏ nhất bị thiếu trong chuỗi REC
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num
            UNION
            SELECT CAST(SUBSTRING(id_receipt, 4) AS UNSIGNED) AS id_num
            FROM Product_receipt
            WHERE id_receipt LIKE 'REC%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Product_receipt
            WHERE CAST(SUBSTRING(id_receipt, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị id_receipt mới
    SET new_id = CONCAT('REC', next_id);
    SET NEW.id_receipt = new_id;
END $$

DELIMITER ;

--tạo id_product
DELIMITER $$

CREATE TRIGGER before_insert_product
BEFORE INSERT ON Products
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng trống, bắt đầu từ prod001
    IF NOT EXISTS (SELECT 1 FROM Products WHERE id_product LIKE 'prod%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm số thứ tự nhỏ nhất chưa dùng (ví dụ: nếu thiếu prod007 thì chèn vào vị trí đó)
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num
            UNION
            SELECT CAST(SUBSTRING(id_product, 5) AS UNSIGNED) AS id_num
            FROM Products
            WHERE id_product LIKE 'prod%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Products
            WHERE CAST(SUBSTRING(id_product, 5) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Định dạng mã id_product với 3 chữ số sau "prod"
    SET new_id = CONCAT('prod', LPAD(next_id, 3, '0'));
    SET NEW.id_product = new_id;
END $$

DELIMITER ;

--tạo id_image
DELIMITER $$

CREATE TRIGGER before_insert_image_product
BEFORE INSERT ON Image_Product
FOR EACH ROW
BEGIN
    DECLARE id_suffix VARCHAR(3);
    DECLARE next_letter CHAR(1);
    DECLARE used_letters TEXT;
    DECLARE letter CHAR(1);
    DECLARE i INT DEFAULT 0;

    -- Lấy 3 ký tự số cuối của id_product (giả sử luôn đúng định dạng prodXXX)
    SET id_suffix = RIGHT(NEW.id_product, 3);

    -- Lấy các ký tự đã dùng cho id_product này (vd: A, B,...)
    SELECT GROUP_CONCAT(SUBSTRING(id_image, 10, 1) ORDER BY SUBSTRING(id_image, 10, 1))
    INTO used_letters
    FROM Image_Product
    WHERE id_product = NEW.id_product;

    -- Tìm ký tự chữ cái đầu tiên chưa dùng (A-Z)
    SET i = 0;
    WHILE i < 26 DO
        SET letter = CHAR(65 + i); -- 65 = 'A', 66 = 'B',...
        IF LOCATE(letter, used_letters) = 0 THEN
            SET next_letter = letter;
            LEAVE;
        END IF;
        SET i = i + 1;
    END WHILE;

    -- Gán id_image mới
    SET NEW.id_image = CONCAT('imaprod', id_suffix, next_letter);
END$$

DELIMITER ;

